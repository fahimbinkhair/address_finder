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
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Statement;
use Doctrine\ORM\QueryBuilder;
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

    /**
     * @param string $postcodePart
     * @return array
     */
    public function getMatchingPostcodes(string $postcodePart): array
    {
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->createQueryBuilder('p')
            ->select('p.postcode')
            ->where("p.postcode LIKE :postcodePart")
            ->setParameter('postcodePart', "%{$postcodePart}%");

        if (strlen($postcodePart) > 3) {
            //to cover AB1 0AA and AB101AA when $postcodePart is AB10
            $queryBuilder->orWhere("p.postcode LIKE :postcodePart2")
                ->setParameter(
                    'postcodePart2',
                    '%' . trim(substr($postcodePart, 0, 3)) . ' ' . trim(substr($postcodePart, 3)) . '%'
                );
        }

        /** @var array $result */
        $result = $queryBuilder->getQuery()->execute();

        return array_column($result, 'postcode');
    }

    /**
     * @param $latitude
     * @param $longitude
     * @param $withinNMiles
     * @return array
     * @throws DBALException
     */
    public function getPostcodesNearALocation($latitude, $longitude, $withinNMiles): array
    {
        $sql = "SELECT postcode, 3956 * 2 * ASIN(SQRT(POWER(SIN(($latitude - abs(latitude)) * pi()/180 / 2), 2)
                    + COS(37 * pi()/180 ) * COS(abs(latitude) * pi()/180)
                    * POWER(SIN(($longitude - longitude) * pi()/180 / 2), 2) )) as  distance
                FROM postcode_info
                HAVING distance <= $withinNMiles
                ORDER BY distance";

        /** @var Connection $conn */
        $conn = $this->getEntityManager()->getConnection();
        /** @var Statement $stmt */
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        /** @var array $result */
        $result = $stmt->fetchAll();

        return array_column($result, 'postcode');
    }
}
