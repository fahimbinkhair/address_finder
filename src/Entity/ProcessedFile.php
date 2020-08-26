<?php
declare(strict_types=1);
/**
 * Description:
 *
 * @package App\Services
 */

namespace App\Entity;

use App\Entity\EntityTrait\ID;
use App\Entity\EntityTrait\DateCreated;
use App\Entity\EntityTrait\DateUpdated;
use App\Repository\ProcessedFileRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProcessedFileRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(
 *     name="processed_file",
 *     uniqueConstraints={
 *          @ORM\UniqueConstraint(name="unique_file_name_and_checksum", columns={"file_name", "md5_checksum"})
 *     }
 * )
 */
class ProcessedFile
{
    use ID, DateCreated, DateUpdated;

    /**
     * @ORM\Column(name="file_name", type="string", length=255)
     */
    private $fileName;

    /**
     * @ORM\Column(name="md5_checksum", type="string", length=45)
     */
    private $md5Checksum;

    /**
     * @return string|null
     */
    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    /**
     * @param string $fileName
     * @return $this
     */
    public function setFileName(string $fileName): self
    {
        $this->fileName = $fileName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getMd5Checksum(): ?string
    {
        return $this->md5Checksum;
    }

    /**
     * @param string $md5Checksum
     * @return $this
     */
    public function setMd5Checksum(string $md5Checksum): self
    {
        $this->md5Checksum = $md5Checksum;

        return $this;
    }
}
