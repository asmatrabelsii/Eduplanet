<?php

namespace App\Controller;

use App\Entity\Cathegories;
use App\Repository\CoursRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/categories')]
class CathegorieController extends AbstractController
{
    #[Route('/{libelle}', name: 'cathegorie_index')]
    public function index(Cathegories $cathegorie, CoursRepository $repo, Request $request): Response
    {
        $cours = $repo->findCours($request->query->getInt('page', 1), $cathegorie);

        return $this->render('cathegories/index.html.twig', [
            'cathegorie' => $cathegorie,
            'cours' => $cours
        ]);
    }
}
