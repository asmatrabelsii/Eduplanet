<?php

namespace App\Manager;

use Datetime;
use App\Entity\Cours;
use App\Entity\Payment;
use App\Entity\Utilisateur;
use App\Service\StripeService;
use App\Repository\PaymentRepository;
use Doctrine\ORM\EntityManagerInterface;

class CoursManager {
    protected $stripeService;
    protected $manager;
    protected $repo;

    public function __construct(StripeService $stripeService, EntityManagerInterface $manager, PaymentRepository $repo)
    {
        $this->stripeService = $stripeService;
        $this->manager = $manager;
        $this->repo = $repo;
    }

    public function intentSecret(Cours $cours)
    {
        $intent = $this->stripeService->paymentIntent($cours);

        return $intent['client_secret'] ?? null;
    }

    public function stripe(array $stripeParameter, Cours $cours)
    {
        $resource = null;
        $data = $this->stripeService->stripe($stripeParameter, $cours);

        if($data) {
            $resource = [
                'stripeBrand' => uniqid('', false),
                'stripeLast4' => uniqid('', false),
                'stripeId' => uniqid('', false),
                'stripeStatus' => $data['status'],
                'stripeToken' => $data['client_secret']
            ];
        }

        return $resource;
    }

    public function create_subscription(array $resource, Cours $cours, Utilisateur $user)
    {
        $payments = $this->repo->findAll();
        $payment = new Payment();
        $payment->setUser($user);
        $payment->setCours($cours);
        $payment->setPrix($cours->getPrix());
        $payment->setReference(uniqid('', false));
        $payment->setBrandStripe($resource['stripeBrand']);
        $payment->setLast4Stripe($resource['stripeLast4']);
        $payment->setIdChargeStripe($resource['stripeId']);
        $payment->setStripeToken($resource['stripeToken']);
        $payment->setStatusStripe($resource['stripeStatus']);
        $payment->setUpdatedAt(new Datetime());
        $payment->setCreatedAt(new \Datetime());

        if($payments !== null){
            $this->manager->persist($payment);
            $this->manager->flush();
    
            $panier = $user->getPanier();
            $panier->removeCour($cours);
            $this->manager->persist($panier);
            $this->manager->flush();
        } else{
            foreach($payments as $paymentt) {
                if($paymentt->getCours() !== $cours && $paymentt->getUser() !== $user) {
                    $this->manager->persist($payment);
                    $this->manager->flush();
            
                    $panier = $user->getPanier();
                    $panier->removeCour($cours);
                    $this->manager->persist($panier);
                    $this->manager->flush();
                }
            }
        }
    }
}