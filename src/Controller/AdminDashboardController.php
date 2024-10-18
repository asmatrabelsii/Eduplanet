<?php

namespace App\Controller;

use App\Service\StatsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractController
{
    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function index(StatsService $statsService)
    {
        $stats      = $statsService->getStats();
        $bestCours  = $statsService->getCoursStats('DESC');
        $worstCours = $statsService->getCoursStats('ASC');

        return $this->render('admin/dashboard/index.html.twig',[
            'stats'      => $stats,
            'bestCours'  => $bestCours,
            'worstCours' => $worstCours
        ]);
    }
}
