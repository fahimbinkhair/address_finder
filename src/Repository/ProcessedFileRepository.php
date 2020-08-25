<?php
declare(strict_types=1);
/**
 * Description:
 *
 * @package App\Services
 */

namespace App\Repository;

use App\Entity\ProcessedFile;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ProcessedFile|null find($id, $lockMode = null, $lockVersion = null)
 * @method ProcessedFile|null findOneBy(array $criteria, array $orderBy = null)
 * @method ProcessedFile[]    findAll()
 * @method ProcessedFile[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProcessedFileRepository extends ServiceEntityRepository
{
    /**
     * ProcessedFileRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProcessedFile::class);
    }
}
