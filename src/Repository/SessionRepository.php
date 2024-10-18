<?php

namespace App\Repository;

use App\Entity\Session;
use App\Model\SearchData;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Session>
 *
 * @method Session|null find($id, $lockMode = null, $lockVersion = null)
 * @method Session|null findOneBy(array $criteria, array $orderBy = null)
 * @method Session[]    findAll()
 * @method Session[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SessionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginatorInterface)
    {
        parent::__construct($registry, Session::class);
    }

    public function save(Session $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Session $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Session[] Returns an array of Session objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Session
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function findCertifications(int $page):PaginationInterface
    {
        $data = $this->createQueryBuilder('s')
                ->orderBy('s.id', 'DESC')
                ->getQuery()
        ;
        $certifications = $this->paginatorInterface->paginate($data, $page, 10);
        return $certifications;
    }

    public function findBySearch (SearchData $searchData):PaginationInterface
    {
        $data = $this->createQueryBuilder('s')
            ->orderBy('s.dateSession', 'DESC');

        if (!empty($searchData->q)) {
            $data = $data
                ->join('s.exam', 'e')
                ->join('s.user', 'u')
                ->join('e.cours', 'c')
                ->andWhere('c.titre LIKE :q')
                ->orWhere('u.nom LIKE :q')
                ->orWhere('u.prenom LIKE :q')
                ->setParameter('q', "%{$searchData->q}%");
        }

        $data = $data
            ->getQuery()
            ->getResult();

        $certifications = $this->paginatorInterface->paginate($data, $searchData->page, 10);

        return $certifications;
    }
}
