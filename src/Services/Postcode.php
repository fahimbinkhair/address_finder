<?php
declare(strict_types=1);
/**
 * Description:
 * download and update postcode database
 *
 * @package App\Services
 */

namespace App\Services;

/**
 * Class Postcode
 *
 * @package App\Services
 */
class Postcode
{
    /** @var bool $loadAllPostcodes */
    private $loadAllPostcodes = false;

    /** @var string $failureReason */
    private $failureReason = '';

    /** @var Downloader $downloader */
    private $downloader;

    /** @var string $areaSpecificFilePath */
    private $areaSpecificFilePath;

    /**
     * Postcode constructor.
     * @param Downloader $downloader
     */
    public function __construct(Downloader $downloader)
    {
        $this->downloader = $downloader;
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
            /** @var string $postcodeLibrary */
            $postcodeLibrary = $this->downloadPostcodeLibrary();
            /** @var array $areaSpecificPostcodeFiles */
            $areaSpecificPostcodeFiles = $this->getAreaSpecificPostcodeFiles($postcodeLibrary);

            //to free up the space
            unlink($postcodeLibrary);
            $this->updateDataBase($areaSpecificPostcodeFiles);

            return true;
        } catch (\Throwable $throwable) {
            $this->failureReason = $throwable->getMessage();

            return false;
        }
    }

    /**
     * download the zip file that contains all information related to the postcode
     * @return string path to post code files
     * @throws \Exception
     */
    private function downloadPostcodeLibrary(): string
    {
        /** @var null|string $postcodeLibrary */
        $postcodeLibrary = null;
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

                $postcodeLibrary = $this->downloader->setFileToDownload($postcodeUri)->download();

                if ($postcodeLibrary !== null && is_file($postcodeLibrary)) {
                    break 2;
                } else {
                    $postcodeUri = null;
                }
            }
        }

        if ($postcodeLibrary === null) {
            throw new \Exception('Can not find the postcode database on mysociety.org');
        }

        return $postcodeLibrary;
    }

    /**
     * return the list of files from {data-source}/Data/multi_csv
     * @param string $postcodeLibrary
     * @return array
     * @throws \Exception
     */
    private function getAreaSpecificPostcodeFiles(string $postcodeLibrary): array
    {
        if (!is_file($postcodeLibrary) || strtolower(pathinfo($postcodeLibrary, PATHINFO_EXTENSION)) !== 'zip') {
            throw new \Exception('Looks like no valid zip file was downloaded');
        }

        $unzipDir = dirname($postcodeLibrary) . DIRECTORY_SEPARATOR . rand(11111, 99999);
        $zip = new \ZipArchive();

        if ($zip->open($postcodeLibrary) === true) {
            $zip->extractTo($unzipDir);
            $zip->close();

            $this->areaSpecificFilePath = $unzipDir . '/Data/multi_csv';

            return array_filter(scandir($this->areaSpecificFilePath), function ($file) {
                return is_file($this->areaSpecificFilePath . DIRECTORY_SEPARATOR . $file);
            });
        }

        throw new \Exception('Failed to unzip the downloaded postcode library');
    }

    /**
     * @param array $areaSpecificPostcodeFiles
     */
    private function updateDataBase(array $areaSpecificPostcodeFiles): void
    {
        foreach ($areaSpecificPostcodeFiles as $areaSpecificPostcodeFile) {
            $filePath = $this->areaSpecificFilePath . DIRECTORY_SEPARATOR . $areaSpecificPostcodeFile;

            if (!is_file($filePath)) {
                continue;
            }

            if ($this->loadAllPostcodes === true && $this->checkThisFileWasProcessed($filePath)) {
                continue;
            }

            //update db with postcode, latitude and longitude
            //record this file as process (filename and checksum)
        }
    }

    /**
     * @param string $areaSpecificPostcodeFile
     * @return bool
     */
    private function checkThisFileWasProcessed(string $areaSpecificPostcodeFile): bool
    {
        //check checksum of this file with db
        return false;
    }
}
