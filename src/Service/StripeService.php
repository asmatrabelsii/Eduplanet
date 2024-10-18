<?php

namespace App\Service;

use Stripe\Stripe;
use App\Entity\Cours;
use Stripe\PaymentIntent;

class StripeService
{
    private $privateKey;

    public function __construct()
    {
        $this->privateKey = $_ENV['STRIPE_SERCRETKEY'];
    }

    public function paymentIntent(Cours $cours)
    {
        Stripe::setApiKey($this->privateKey);

        return PaymentIntent::create([
            'amount' => intval($cours->getPrix() * 1000),
            'currency' => 'tnd',
            'payment_method_types' => ['card']        
        ]);
    }

    public function paiement(
        $amount,
        $currency,
        $description,
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

    public function stripe(array $stripeParameter, Cours $cours)
    {
        return $this->paiement(
            intval($cours->getPrix() * 1000),
            'tnd',
            $cours->getTitre(),
            $stripeParameter
        );
    }
}