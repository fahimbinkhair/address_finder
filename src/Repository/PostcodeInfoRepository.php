<?php
declare(strict_types=1);
/**
 * Description:
 *
 * @package App\Services
 */

namespace App\Repository;

use App\Entity\PostcodeInfo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PostcodeInfo|null find($id, $lockMode = null, $lockVersion = null)
 * @method PostcodeInfo|null findOneBy(array $criteria, array $orderBy = null)
 * @method PostcodeInfo[]    findAll()
 * @method PostcodeInfo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostcodeInfoRepository extends ServiceEntityRepository
{
    /**
     * PostcodeInfoRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PostcodeInfo::class);
    }
}
