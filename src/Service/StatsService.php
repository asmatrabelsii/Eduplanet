<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class StatsService {
    private $manager;

    public function __construct(EntityManagerInterface $manager) {
        $this->manager = $manager;
    }

    public function getStats(){
        $users = $this->getUsersCount();
        $cours = $this->getCoursCount();
        $comments = $this->getCommentsCount();
        $payments = $this->getPaymentsCount();

        return compact('users', 'cours', 'comments', 'payments');
    }

    public function getUsersCount() {
        return $this->manager->createQuery('SELECT COUNT(u) FROM App\entity\Utilisateur u')->getSingleScalarResult();
    }

    public function getCoursCount() {
        return $this->manager->createQuery('SELECT COUNT(c) FROM App\entity\Cours c')->getSingleScalarResult();
    }

    public function getCommentsCount() {
        return $this->manager->createQuery('SELECT COUNT(com) FROM App\entity\Commentaire com')->getSingleScalarResult();
    }

    public function getPaymentsCount() {
        return $this->manager->createQuery('SELECT COUNT(com) FROM App\entity\Payment com')->getSingleScalarResult();
    }

    public function getCoursStats($direction)
    {
        return $this->manager->createQuery(
            'SELECT AVG(com.rating) as note, c.titre, c.id, u.prenom, u.nom, u.avatar
            FROM App\entity\Commentaire com
            JOIN com.cours c
            JOIN c.auteur u
            GROUP BY c
            ORDER BY note '. $direction
        )->setMaxResults(5)->getResult();
    }
}