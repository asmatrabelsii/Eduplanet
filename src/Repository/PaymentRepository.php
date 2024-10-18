<?php

namespace App\Repository;

use App\Entity\Payment;
use App\Model\SearchData;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Payment>
 *
 * @method Payment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Payment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Payment[]    findAll()
 * @method Payment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginatorInterface)
    {
        parent::__construct($registry, Payment::class);
    }

    public function save(Payment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Payment $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Payment[] Returns an array of Payment objects
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

//    public function findOneBySomeField($value): ?Payment
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function findPayments(int $page):PaginationInterface
    {
        $data = $this->createQueryBuilder('p')
                ->orderBy('p.id', 'DESC')
                ->getQuery()
        ;
        $payments = $this->paginatorInterface->paginate($data, $page, 10);
        return $payments;
    }

    public function findBySearch (SearchData $searchData):PaginationInterface
    {
        $data = $this->createQueryBuilder('p')
            ->orderBy('p.createdAt', 'DESC');

        if (!empty($searchData->q)) {
            $data = $data
                ->join('p.cours', 'co')
                ->join('p.user', 'u')
                ->andWhere('co.titre LIKE :q')
                ->orWhere('p.prix LIKE :q')
                ->orWhere('u.nom LIKE :q')
                ->orWhere('u.prenom LIKE :q')
                ->setParameter('q', "%{$searchData->q}%");
        }

        $data = $data
            ->getQuery()
            ->getResult();

        $payments = $this->paginatorInterface->paginate($data, $searchData->page, 10);

        return $payments;
    }
}
