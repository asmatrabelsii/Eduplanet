<?php

namespace App\Repository;

use App\Entity\Cathegories;
use App\Entity\Cours;
use App\Model\SearchData;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Cours>
 *
 * @method Cours|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cours|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cours[]    findAll()
 * @method Cours[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CoursRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginatorInterface)
    {
        parent::__construct($registry, Cours::class);
    }

    public function save(Cours $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Cours $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Cours[] Returns an array of Cours objects
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

//    public function findOneBySomeField($value): ?Cours
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function findCours(int $page, ?Cathegories $cathegorie = null): PaginationInterface
    {
        $data = $this->createQueryBuilder('c')
            ->select('c', 'cat')
            ->join('c.cathegorie', 'cat')
            ->orderBy('c.id', 'DESC');

        if (isset($cathegorie)) {
            $data = $data
                ->andWhere('cat.id = :cathegorie')
                ->setParameter('cathegorie', $cathegorie->getId());
        }

        $data = $data
            ->getQuery()
            ->getResult();

        $cours = $this->paginatorInterface->paginate($data, $page, 10);

        return $cours;
    }
    
    public function findBySearch (SearchData $searchData):PaginationInterface
    {
        $data = $this->createQueryBuilder('c')
            ->orderBy('c.id', 'DESC');
        if (!empty($searchData->q)) {
            $data = $data
                ->join('c.cathegorie', 'cat')
                ->join('c.auteur', 'a')
                ->andWhere('c.titre LIKE :q')
                ->orWhere('c.description LIKE :q')
                ->orWhere('cat.libelle LIKE :q')
                ->orWhere('a.nom LIKE :q')
                ->orWhere('a.prenom LIKE :q')
                ->setParameter('q', "%{$searchData->q}%");
        }

        $data = $data
            ->getQuery()
            ->getResult();

        $cours = $this->paginatorInterface->paginate($data, $searchData->page, 10);

        return $cours;
    }
}
