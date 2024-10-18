<?php

namespace App\Repository;

use App\Model\SearchData;
use App\Entity\PaymentExamen;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<PaymentExamen>
 *
 * @method PaymentExamen|null find($id, $lockMode = null, $lockVersion = null)
 * @method PaymentExamen|null findOneBy(array $criteria, array $orderBy = null)
 * @method PaymentExamen[]    findAll()
 * @method PaymentExamen[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PaymentExamenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginatorInterface)
    {
        parent::__construct($registry, PaymentExamen::class);
    }

    public function save(PaymentExamen $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(PaymentExamen $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return PaymentExamen[] Returns an array of PaymentExamen objects
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

//    public function findOneBySomeField($value): ?PaymentExamen
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
                ->join('p.examen', 'e')
                ->join('p.user', 'u')
                ->join('e.cours', 'c')
                ->andWhere('c.titre LIKE :q')
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
