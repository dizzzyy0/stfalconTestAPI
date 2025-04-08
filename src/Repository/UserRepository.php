<?php
declare(strict_types=1);
namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }


    public function getAllUsers(int $offset, int $limit): array{
        try {
            $query = $this->createQueryBuilder('u')
                ->setFirstResult($offset)
                ->setMaxResults($limit)
                ->getQuery();
            $paginator = new Paginator($query);
            return [
                'result'=> iterator_to_array($paginator),
                'total' => $paginator->count(),
                'offset' => $offset,
                'limit' => $limit,
            ];
        } catch (\Exception $e) {
            throw new \Exception("Unable to fetch users: " . $e->getMessage());
        }
    }
    //    /**
    //     * @return User[] Returns an array of User objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?User
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
