<?php

namespace App\Repository;

use App\Model\SearchData;
use App\Entity\Commentaire;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Commentaire>
 *
 * @method Commentaire|null find($id, $lockMode = null, $lockVersion = null)
 * @method Commentaire|null findOneBy(array $criteria, array $orderBy = null)
 * @method Commentaire[]    findAll()
 * @method Commentaire[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentaireRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginatorInterface)
    {
        parent::__construct($registry, Commentaire::class);
    }

    public function save(Commentaire $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Commentaire $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Commentaire[] Returns an array of Commentaire objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Commentaire
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function findComments(int $page):PaginationInterface
    {
        $data = $this->createQueryBuilder('c')
                ->orderBy('c.id', 'DESC')
                ->getQuery()
        ;
        $comments = $this->paginatorInterface->paginate($data, $page, 10);
        return $comments;
    }

    public function findBySearch (SearchData $searchData):PaginationInterface
    {
        $data = $this->createQueryBuilder('c')
            ->orderBy('c.createdAt', 'DESC');

        if (!empty($searchData->q)) {
            $data = $data
                ->join('c.cours', 'co')
                ->join('c.auteur', 'a')
                ->andWhere('co.titre LIKE :q')
                ->orWhere('c.contenu LIKE :q')
                ->orWhere('a.nom LIKE :q')
                ->orWhere('a.prenom LIKE :q')
                ->setParameter('q', "%{$searchData->q}%");
        }

        $data = $data
            ->getQuery()
            ->getResult();

        $comments = $this->paginatorInterface->paginate($data, $searchData->page, 10);

        return $comments;
    }
}
