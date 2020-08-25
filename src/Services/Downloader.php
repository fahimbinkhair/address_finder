<?php
declare(strict_types=1);
/**
 * Description:
 * Download any file from web
 *
 * @package App\Services
 *
 * @copyright 2020 Data Interconnect Ltd.
 */

namespace App\Services;

/**
 * Class Downloader
 *
 * @package App\Services
 */
class Downloader
{
    /** @var string $fileToDownload */
    private $fileToDownload;

    /**
     * @param string $fileToDownload
     * @return $this
     */
    public function setFileToDownload(string $fileToDownload): self
    {
        $this->fileToDownload = $fileToDownload;

        return $this;
    }

    /**
     * @return string|null path to the downloaded file
     * @throws \Exception
     */
    public function download(): ?string
    {
        if (false === $this->checkFileExists()) {
            return null;
        }

        $downloadedFilePath = '/tmp/' . basename($this->fileToDownload);
        //This is the file where we save the file
        $downloadedFile = fopen($downloadedFilePath, 'w+');
        //Here is the file we are downloading, replace spaces with %20
        $curl = curl_init(str_replace(" ", "%20", $this->fileToDownload));
        curl_setopt($curl, CURLOPT_TIMEOUT, 50);
        // write curl response to file
        curl_setopt($curl, CURLOPT_FILE, $downloadedFile);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_exec($curl);
        curl_close($curl);
        fclose($downloadedFile);

        if (!is_file($downloadedFilePath)) {
            throw new \Exception('Failed to download from ' . $this->fileToDownload);
        }

        return $downloadedFilePath;
    }

    /**
     * @return bool
     */
    private function checkFileExists(): bool
    {
        $curl = curl_init($this->fileToDownload);

        //don't fetch the actual page as we only want to check the connection is ok
        curl_setopt($curl, CURLOPT_NOBODY, true);
        $result = curl_exec($curl);
        $ret = false;

        if ($result !== false) {
            $statusCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

            if ($statusCode == 200) {
                $ret = true;
            }
        }

        curl_close($curl);

        return $ret;
    }
}
