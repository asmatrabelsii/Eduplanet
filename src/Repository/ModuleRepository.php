<?php

namespace App\Repository;

use App\Entity\Cours;
use App\Entity\Module;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Component\Pager\Pagination\PaginationInterface;use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Module>
 *
 * @method Module|null find($id, $lockMode = null, $lockVersion = null)
 * @method Module|null findOneBy(array $criteria, array $orderBy = null)
 * @method Module[]    findAll()
 * @method Module[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private PaginatorInterface $paginatorInterface)
    {
        parent::__construct($registry, Module::class);
    }

    public function save(Module $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Module $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Module[] Returns an array of Module objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Module
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function findModule(int $page, Cours $cours): PaginationInterface
    {
        $data = $this->createQueryBuilder('m')
            ->orderBy('m.id', 'ASC');

        if ($cours) {
            $data = $data
                ->andWhere('m.cours = :cours')
                ->setParameter('cours', $cours);
        }

        $data = $data
            ->getQuery()
            ->getResult();

        $module = $this->paginatorInterface->paginate($data, $page, 1);

        return $module;
    }
}
