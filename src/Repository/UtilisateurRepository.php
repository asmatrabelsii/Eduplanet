<?php

namespace App\Repository;

use App\Model\SearchData;
use App\Entity\Utilisateur;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Utilisateur>
 *
 * @method Utilisateur|null find($id, $lockMode = null, $lockVersion = null)
 * @method Utilisateur|null findOneBy(array $criteria, array $orderBy = null)
 * @method Utilisateur[]    findAll()
 * @method Utilisateur[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UtilisateurRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginatorInterface)
    {
        parent::__construct($registry, Utilisateur::class);
    }

    public function save(Utilisateur $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Utilisateur $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Utilisateur[] Returns an array of Utilisateur objects
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

//    public function findOneBySomeField($value): ?Utilisateur
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function findUtilisateurs(int $page): PaginationInterface
    {
        $data = $this->createQueryBuilder('u')
            ->orderBy('u.id', 'DESC')
            ->getQuery();

        $utilisateurs = $this->paginatorInterface->paginate($data, $page, 10);

        return $utilisateurs;
    }

    public function findBySearch (SearchData $searchData):PaginationInterface
    {
        $data = $this->createQueryBuilder('u')
            ->orderBy('u.id', 'DESC');

        if (!empty($searchData->q)) {
            $data = $data
                ->Where('u.nom LIKE :q')
                ->orWhere('u.prenom LIKE :q')
                ->orWhere('u.email LIKE :q')
                ->orWhere('u.cin LIKE :q')
                ->setParameter('q', "%{$searchData->q}%");
        }

        $data = $data
            ->getQuery()
            ->getResult();

        $utilisateurs = $this->paginatorInterface->paginate($data, $searchData->page, 10);

        return $utilisateurs;
    }
}
