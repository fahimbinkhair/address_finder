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
use App\Repository\PostcodeInfoRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PostcodeInfoRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Table(name="postcode_info")
 */
class PostcodeInfo
{
    use ID, DateCreated, DateUpdated;

    /**
     * @ORM\Column(name="postcode", type="string", length=45, unique=true)
     */
    private $postcode;

    /**
     * @ORM\Column(name="latitude", type="decimal", precision=10, scale=8)
     */
    private $latitude;

    /**
     * @ORM\Column(name="longitude", type="decimal", precision=11, scale=8)
     */
    private $longitude;

    /**
     * @return string|null
     */
    public function getPostcode(): ?string
    {
        return $this->postcode;
    }

    /**
     * @param string $postcode
     * @return $this
     */
    public function setPostcode(string $postcode): self
    {
        $this->postcode = $postcode;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLatitude(): ?string
    {
        return $this->latitude;
    }

    /**
     * @param string $latitude
     * @return $this
     */
    public function setLatitude(string $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getLongitude(): ?string
    {
        return $this->longitude;
    }

    /**
     * @param string $longitude
     * @return $this
     */
    public function setLongitude(string $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }
}
