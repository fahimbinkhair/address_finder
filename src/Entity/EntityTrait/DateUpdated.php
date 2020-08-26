<?php
declare(strict_types=1);
/**
 * Description:
 * the field date_updated
 *
 * @package App\Entity\EntityTrait
 *
 * @copyright 2020 Data Interconnect Ltd.
 */

namespace App\Entity\EntityTrait;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait DateUpdated
 *
 * @package App\Entity\EntityTrait
 */
trait DateUpdated
{
    /**
     * @ORM\Column(name="date_updated", type="datetime", nullable=true)
     */
    private $dateUpdated;

    /**
     * @return \DateTimeInterface|null
     */
    public function getDateUpdated(): ?\DateTimeInterface
    {
        return $this->dateUpdated;
    }

    /**
     * @param \DateTimeInterface $dateUpdated
     * @return self
     */
    public function setDateUpdated(\DateTimeInterface $dateUpdated): self
    {
        $this->dateUpdated = new \DateTime();

        return $this;
    }
}
