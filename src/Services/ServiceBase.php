<?php
declare(strict_types=1);
/**
 * Description:
 * contains basis functionalities for services
 *
 * @package App\Services
 */

namespace App\Services;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Class ServiceBase
 *
 * @package App\Services
 */
class ServiceBase
{
    /** @var EntityManagerInterface $em */
    protected $em;

    /**
     * ServiceBase constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }
}
