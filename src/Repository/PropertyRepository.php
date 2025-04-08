<?php
declare(strict_types=1);
namespace App\Repository;

use App\Entity\Property;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Property>
 */
class PropertyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Property::class);
    }

    public function getPropertiesForUser(int $offset, int $limit, $visibleStatuses):array{
        try {
            $query = $this->createQueryBuilder('p')
                ->where('p.status IN (:statuses)')
                ->setParameter('statuses', $visibleStatuses)
                ->setFirstResult($offset)
                ->setMaxResults($limit)
                ->getQuery();

            $paginator = new Paginator($query);

            return [
                'result' => iterator_to_array($paginator),
                'total' => count($paginator),
                'offset' => $offset,
                'limit' => $limit,
            ];
        } catch (\Exception $e) {
            throw new \Exception("Unable to fetch properties: " . $e->getMessage());
        }
    }

    public function getAllProperties(int $offset, int $limit):array{
        try {
            $query = $this->createQueryBuilder('p')
                ->setFirstResult($offset)
                ->setMaxResults($limit)
                ->getQuery();

            $paginator = new Paginator($query);
            return[
                'result' => iterator_to_array($paginator),
                'total' => $paginator->count(),
                'offset' => $offset,
                'limit' => $limit,
            ];
        } catch (\Exception $e) {
            throw new \Exception("Unable to fetch properties: " . $e->getMessage());
        }
    }

    //    /**
    //     * @return Property[] Returns an array of Property objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Property
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
