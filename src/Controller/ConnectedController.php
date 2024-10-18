<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConnectedController extends AbstractController
{
    #[Route('/connected', name: 'app_connected')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted("IS_AUTHENTICATED_FULLY");
        /** @var Utilisateur $user */
        $user = $this->getUser();

        return match ($user->isVerified()) {
            true => $this->render('connected/connectedpage.html.twig'),
            false => $this->render('connected/verifyemail.html.twig'),
        };
    }
}
