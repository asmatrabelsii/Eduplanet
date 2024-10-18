<?php

namespace App\Service;

use Stripe\Stripe;
use App\Entity\Examen;
use Stripe\PaymentIntent;

class ExamService
{
    private $privateKey;

    public function __construct()
    {
        $this->privateKey = $_ENV['STRIPE_SERCRETKEY'];
    }

    public function paymentIntent(Examen $examen)
    {
        Stripe::setApiKey($this->privateKey);

        return PaymentIntent::create([
            'amount' => intval($examen->getPrix() * 1000),
            'currency' => 'tnd',
            'payment_method_types' => ['card']        
        ]);
    }

    public function paiement(
        $amount,
        $currency,
        array $stripeParameter
    )
    {
        \Stripe\Stripe::setApiKey($this->privateKey);
        $payment_intent = null;

        if(isset($stripeParameter['stripeIntentId'])) {
            $payment_intent = PaymentIntent::retrieve($stripeParameter['stripeIntentId']);
        }

        if($stripeParameter['stripeIntentStatus'] === 'succeeded') {
            //TODO
        } else {
            $payment_intent->cancel();
        }

        return $payment_intent;
    }

    public function stripe(array $stripeParameter, Examen $examen)
    {
        return $this->paiement(
            intval($examen->getPrix() * 1000),
            'tnd',
            $stripeParameter
        );
    }
}