<?php

namespace App\Manager;

use Datetime;
use App\Entity\Examen;
use App\Entity\PaymentExamen;
use App\Entity\Utilisateur;
use App\Repository\PaymentExamenRepository;
use App\Service\ExamService;
use Doctrine\ORM\EntityManagerInterface;

class ExamenManager {
    protected $examService;
    protected $manager;
    protected $repo;

    public function __construct(ExamService $examService, EntityManagerInterface $manager, PaymentExamenRepository $repo)
    {
        $this->examService = $examService;
        $this->manager = $manager;
        $this->repo = $repo;
    }

    public function intentSecret(Examen $examen)
    {
        $intent = $this->examService->paymentIntent($examen);

        return $intent['client_secret'] ?? null;
    }

    public function stripe(array $stripeParameter, Examen $examen)
    {
        $resource = null;
        $data = $this->examService->stripe($stripeParameter, $examen);

        if($data) {
            $resource = [
                'stripeStatus' => $data['status'],
                'stripeToken' => $data['client_secret']
            ];
        }

        return $resource;
    }

    public function create_subscription(array $resource, Examen $examen, Utilisateur $user)
    {
        $payments = $this->repo->findAll();
        $payment = new PaymentExamen();
        $payment->setUser($user);
        $payment->setExamen($examen);
        $payment->setPrix($examen->getPrix());
        $payment->setReference(uniqid('', false));
        $payment->setStripeToken($resource['stripeToken']);
        $payment->setStatusStripe($resource['stripeStatus']);
        $payment->setUpdatedAt(new Datetime());
        $payment->setCreatedAt(new \Datetime());

        $this->manager->persist($payment);
        $this->manager->flush();
    }
}