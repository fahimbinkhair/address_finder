<?php
declare(strict_types=1);
/**
 * Description:
 * the field id
 *
 * @package App\Entity\EntityTrait
 *
 * @copyright 2020 Data Interconnect Ltd.
 */

namespace App\Entity\EntityTrait;

/**
 * Trait ID
 *
 * @package App\Entity\EntityTrait
 */
trait ID
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }
}
