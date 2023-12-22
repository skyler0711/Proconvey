<?php

namespace App\GraphQL\Mutations\Conveyancer;

use Stripe\Exception\OAuth\OAuthErrorException;
use Stripe\StripeClient;

final class DisconnectStripe
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        /** @var \App\Models\Conveyancer */
        $conveyancer = auth()->user()->conveyancer;

        /** @var \Stripe\StripeClient */
        $stripe = app()->make(StripeClient::class);

        try {
            $stripe->oauth->deauthorize([
                'stripe_user_id' => $conveyancer->stripe_account_id,
            ]);
        } catch (OAuthErrorException $e) {
            if ($e->getStripeCode() !== 'invalid_client') {
                // Invalid client means the account was already disconnected
                throw $e;
            }
        }

        $conveyancer->stripe_account_id = null;
        $conveyancer->save();

        return true;
    }
}
