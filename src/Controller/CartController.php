<?php

namespace App\Controller;

use App\Entity\Cours;
use App\Manager\CoursManager;
use App\Repository\PanierRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{

    #[IsGranted('ROLE_APPRENANT')]
    #[Route('/paiement/{id}/show', name:'paiement',methods:['GET', 'POST'])]
    public function payment(Cours $cours, CoursManager $coursManager): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        return $this->render('cart/pay.html.twig', [
            'user' => $this->getUser(),
            'intentSecret' => $coursManager->intentSecret($cours),
            'cours' => $cours
        ]);
    }

    #[IsGranted('ROLE_APPRENANT')]
    #[Route('/user/subscription/{id}/paiement/load', name:'subscription_paiement',methods:['GET', 'POST'])]
    public function subsciption(Cours $cours, Request $request, CoursManager $coursManager)
    {
        $user = $this->getUser();

        if($request->getMethod() === "POST") {
            $resource = $coursManager->stripe($_POST, $cours);

            if(null !== $resource) {
                $coursManager->create_subscription($resource, $cours, $user);

                return $this->render('payment/success.html.twig', [
                    'product' => $cours
                ]);
            }
        }

        return $this->redirectToRoute('paiement', ['id' => $cours->getId()]);
    }

    #[Route('/cart', name: 'cart_index')]
    #[IsGranted('ROLE_APPRENANT')]
    public function index(PanierRepository $panierRepository)
    {
        $panier = $panierRepository->findOneBy(['owner' => $this->getUser()]);
        $courses = $panier->getCours();
        $total = 0;
    
        foreach($courses as $cours){
            $total += $cours->getPrix();
        }
    
        return $this->render('cart/index.html.twig', [
            'panier' => $panier,
            'total'=> $total
        ]);
    }

    #[Route('/cart/add/{id}', name: 'cart_add')]
    #[IsGranted('ROLE_APPRENANT')]
    public function add(Cours $cours, EntityManagerInterface $manager, PanierRepository $panierRepository)
    {
        $panier = $panierRepository->findOneBy(['owner' => $this->getUser()]);
        
        $panier->addCour($cours);
        $cours->addPanier($panier);

        $manager->persist($panier);
        $manager->flush();
        $manager->persist($cours);
        $manager->flush();
        
        return $this->redirectToRoute("cart_index");
    }

    #[Route('/cart/remove/{id}', name: 'cart_remove')]
    #[IsGranted('ROLE_APPRENANT')]
    public function remove(Cours $cours, EntityManagerInterface $manager, PanierRepository $panierRepository)
    {
        $panier = $panierRepository->findOneBy(['owner' => $this->getUser()]);
        
        $panier->removeCour($cours);
        $cours->removePanier($panier);

        $manager->persist($panier);
        $manager->flush();
        $manager->persist($cours);
        $manager->flush();
        
        return $this->redirectToRoute("cart_index");
    }

    #[Route('/cart/removeAll', name: 'cart_removeAll')]
    #[IsGranted('ROLE_APPRENANT')]
    public function removeAll(EntityManagerInterface $manager, PanierRepository $panierRepository)
    {
        $panier = $panierRepository->findOneBy(['owner' => $this->getUser()]);
        
        foreach ($panier->getCours() as $cours)
        {
            $panier->removeCour($cours);
            $cours->removePanier($panier);
            $manager->persist($cours);
            $manager->flush();
        }

        $manager->persist($panier);
        $manager->flush();
        
        return $this->redirectToRoute("cart_index");
    }
}
