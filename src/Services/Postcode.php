<?php
declare(strict_types=1);
/**
 * Description:
 * download and update postcode database
 *
 * @package App\Services
 */

namespace App\Services;

use App\Entity\PostcodeInfo;
use App\Entity\ProcessedFile;
use App\Repository\PostcodeInfoRepository;
use App\Repository\ProcessedFileRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class Postcode
 *
 * @package App\Services
 */
class Postcode extends ServiceBase
{
    /** @var bool $loadAllPostcodes */
    private $loadAllPostcodes = false;

    /** @var string $failureReason */
    private $failureReason = '';

    /** @var Downloader $downloader */
    private $downloader;

    /** @var string $areaSpecificFilePath */
    private $areaSpecificFilePath;

    /** @var ProcessedFileRepository $processedFileRepository */
    private $processedFileRepository;

    /** @var PostcodeInfoRepository $postcodeInfoRepository */
    private $postcodeInfoRepository;

    /** @var ProcessedFile|null $processedFile */
    private $processedFile;

    /**
     * Postcode constructor.
     * @param EntityManagerInterface $entityManager
     * @param Downloader $downloader
     */
    public function __construct(EntityManagerInterface $entityManager, Downloader $downloader)
    {
        parent::__construct($entityManager);
        $this->downloader = $downloader;
        $this->postcodeInfoRepository = $this->em->getRepository(PostcodeInfo::class);
        $this->processedFileRepository = $this->em->getRepository(ProcessedFile::class);
    }

    /**
     * By default only changed/update file from {data-source}/Data/multi_csv will be processed
     * but you can force to reload/process all file
     * @param bool $loadAllPostcodes
     * @return $this
     */
    public function setLoadAllPostcodes(bool $loadAllPostcodes): self
    {
        $this->loadAllPostcodes = $loadAllPostcodes;

        return $this;
    }

    /**
     * @return string
     */
    public function getFailureReason(): string
    {
        return $this->failureReason;
    }

    /**
     * update the postcode database
     * @return bool
     */
    public function loadPostcode(): bool
    {
        try {
            $success = true;
            /** @var string $postcodeLibraryZip */
            $postcodeLibraryZip = $this->downloadPostcodeLibrary();
            /** @var array $areaSpecificPostcodeFiles */
            $areaSpecificPostcodeFiles = $this->getAreaSpecificPostcodeFiles($postcodeLibraryZip);

            //to free up the space
            unlink($postcodeLibraryZip);

            foreach ($areaSpecificPostcodeFiles as $areaSpecificPostcodeFile) {
                $areaSpecificPostcodeFilePath = $this->areaSpecificFilePath
                    . DIRECTORY_SEPARATOR .
                    $areaSpecificPostcodeFile;

                if (!is_file($areaSpecificPostcodeFilePath)) {
                    continue;
                }

                /** @var false|string $fileChecksum */
                $fileChecksum = md5_file($areaSpecificPostcodeFilePath);

                if ($fileChecksum === false) {
                    throw new \Exception("Can not generate checksum for the file '{$areaSpecificPostcodeFilePath}'");
                }

                if ($this->loadAllPostcodes === false && $this->checkThisFileWasProcessed($fileChecksum)) {
                    continue;
                }

                $this->updatePostcodeInfoTable($areaSpecificPostcodeFilePath);
                $this->addToProcessedFileList($areaSpecificPostcodeFile, $fileChecksum);
                $this->processedFile = null;
            }
        } catch (\Throwable $throwable) {
            $this->failureReason = $throwable->getMessage();
            $success = false;
        }

        //remove the downloaded data
        if (is_file($this->areaSpecificFilePath . DIRECTORY_SEPARATOR . $areaSpecificPostcodeFiles[0])) {
            //will be implemented in the future
        }

        return $success;
    }

    /**
     * download the zip file that contains all information related to the postcode
     * @return string path to post code files
     * @throws \Exception
     */
    private function downloadPostcodeLibrary(): string
    {
        /** @var null|string $postcodeLibraryZip */
        $postcodeLibraryZip = null;
        /** @var int $tillYear */
        $tillYear = date('Y') - 5;

        for ($i = date('Y'); $i > $tillYear; $i--) {
            for ($j = 12; $j > 0; $j--) {
                /** @var string $postcodeUri */
                $postcodeUri = sprintf(
                    'https://parlvid.mysociety.org/os/ONSPD/%s-%s.zip',
                    $i,
                    str_pad((string)$j, 2, '0', STR_PAD_LEFT)
                );

                $postcodeLibraryZip = $this->downloader->setFileToDownload($postcodeUri)->download();

                if ($postcodeLibraryZip !== null && is_file($postcodeLibraryZip)) {
                    break 2;
                } else {
                    $postcodeUri = null;
                }
            }
        }

        if ($postcodeLibraryZip === null) {
            throw new \Exception('Can not find the postcode database on mysociety.org');
        }

        return $postcodeLibraryZip;
    }

    /**
     * return the list of files from {data-source}/Data/multi_csv
     * @param string $postcodeLibraryZip
     * @return array
     * @throws \Exception
     */
    private function getAreaSpecificPostcodeFiles(string $postcodeLibraryZip): array
    {
        if (!is_file($postcodeLibraryZip) || strtolower(pathinfo($postcodeLibraryZip, PATHINFO_EXTENSION)) !== 'zip') {
            throw new \Exception('Looks like no valid zip file was downloaded');
        }

        $unzipDir = dirname($postcodeLibraryZip) . DIRECTORY_SEPARATOR . rand(11111, 99999);
        $zip = new \ZipArchive();

        if ($zip->open($postcodeLibraryZip) === true) {
            $zip->extractTo($unzipDir);
            $zip->close();

            $this->areaSpecificFilePath = $unzipDir . '/Data/multi_csv';

            $areaSpecificPostcodeFiles = array_filter(scandir($this->areaSpecificFilePath), function ($file) {
                return is_file($this->areaSpecificFilePath . DIRECTORY_SEPARATOR . $file);
            });

            return array_values($areaSpecificPostcodeFiles); //to fix the index numbers
        }

        throw new \Exception('Failed to unzip the downloaded postcode library');
    }

    /**
     * @param string $fileChecksum
     * @return bool
     */
    private function checkThisFileWasProcessed(string $fileChecksum): bool
    {
        $this->setProcessedFile($fileChecksum);

        return $this->processedFile instanceof ProcessedFile;
    }

    /**
     * @param string $fileChecksum
     */
    private function setProcessedFile(string $fileChecksum): void
    {
        $this->processedFile = $this->processedFileRepository->findOneBy(['md5Checksum' => $fileChecksum]);
    }

    /**
     * @param string $areaSpecificPostcodeFile
     * @throws \Exception
     */
    private function updatePostcodeInfoTable(string $areaSpecificPostcodeFile): void
    {
        $recordCounter = 0;
        /** @var resource $fileHandle */
        $fileHandle = fopen($areaSpecificPostcodeFile, 'r');

        if (!$fileHandle) {
            throw new \Exception("Can not open the file '$areaSpecificPostcodeFile'");
        }

        /** @var array $firstLine */
        $firstLine = fgetcsv($fileHandle, 1000, ',');

        if ($firstLine[0] !== 'pcd' || $firstLine[42] !== 'lat' || $firstLine[43] !== 'long') {
            throw new \Exception('Can not process the file as column positions has been changed');
        }

        while (($line = fgetcsv($fileHandle, 1000, ',')) !== false) {
            $recordCounter++;
            $postcode = $line[0];
            $latitude = $line[42];
            $longitude = $line[43];

            /** @var PostcodeInfo $postcodeInfo */
            $postcodeInfo = $this->postcodeInfoRepository->findOneBy(['postcode' => $postcode]);

            if ($postcodeInfo instanceof PostcodeInfo) {
                $postcodeInfo->setPostcode($postcode)
                    ->setLatitude($latitude)
                    ->setLongitude($longitude)
                    ->setDateUpdated(new \DateTime());
            } else {
                $postcodeInfo = new PostcodeInfo();
                $postcodeInfo->setPostcode($postcode)
                    ->setLatitude($latitude)
                    ->setLongitude($longitude);
                $this->em->persist($postcodeInfo);
            }

            //flushing after every record may cause performance issue
            //so flush and clear after every n record to free up the memory
            if ($recordCounter % 250 === 0) {
                $this->em->flush();
                $this->em->clear();
                Logger::log(LOG_INFO, "Saved/updated {$recordCounter} records so far");
            }
        }

        //a final flush and clear for any left out record
        $this->em->flush();
        $this->em->clear();
        Logger::log(LOG_INFO, "Saved total {$recordCounter} records from the file '{$areaSpecificPostcodeFile}'");

        fclose($fileHandle);
    }

    /**
     * @param string $fileName
     * @param string $fileChecksum
     */
    private function addToProcessedFileList(string $fileName, string $fileChecksum): void
    {
        if ($this->loadAllPostcodes === true) {
            $this->setProcessedFile($fileChecksum);
        }

        if ($this->processedFile instanceof ProcessedFile) {
            $this->processedFile->setFileName($fileName)
                ->setMd5Checksum($fileChecksum)
                ->setDateUpdated(new \DateTime());

            return;
        }

        $processedFile = new ProcessedFile();
        $processedFile->setFileName($fileName)->setMd5Checksum($fileChecksum);
        $this->em->persist($processedFile);
        $this->em->flush();
    }
}
