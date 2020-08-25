<?php
declare(strict_types=1);
/**
 * Description:
 * the field date_created
 *
 * @package App\Entity\EntityTrait
 *
 * @copyright 2020 Data Interconnect Ltd.
 */

namespace App\Entity\EntityTrait;

/**
 * Trait DateCreated
 *
 * @package App\Entity\EntityTrait
 */
trait DateCreated
{
    /**
     * @ORM\Column(name="date_created", type="datetime")
     */
    private $dateCreated;

    /**
     * @return \DateTimeInterface|null
     */
    public function getDateCreated(): ?\DateTimeInterface
    {
        return $this->dateCreated;
    }

    /**
     * @ORM\PrePersist()
     * @return self
     */
    public function setDateCreated(): self
    {
        $this->dateCreated = new \DateTime();

        return $this;
    }
}
