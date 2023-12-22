<?php

namespace App\GraphQL\Mutations;

final class UpdateConveyancer
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        /** @var \App\Models\Conveyancer */
        $conveyancer = auth()->user()->conveyancer;

        $newData = [
            ...optional($args)['client_care_letter'] ? ['client_care_letter' => $args['client_care_letter']] : [],
            ...optional($args)['client_care_letter_sale'] ? ['client_care_letter_sale' => $args['client_care_letter_sale']] : [],
            ...optional($args)['client_care_letter_purchase'] ? ['client_care_letter_purchase' => $args['client_care_letter_purchase']] : [],
            ...optional($args)['client_care_letter_remortgage'] ? ['client_care_letter_remortgage' => $args['client_care_letter_remortgage']] : [],
            ...optional($args)['terms_and_conditions'] ? ['terms_and_conditions' => $args['terms_and_conditions']] : [],
            ...optional($args)['letter_header'] ? ['letter_header' => $args['letter_header']] : [],
            ...optional($args)['letter_footer'] ? ['letter_footer' => $args['letter_footer']] : [],
            ...optional($args)['payment_on_account_amount'] ? ['payment_on_account_amount' => $args['payment_on_account_amount']] : [],
        ];

        $conveyancer->update($newData);

        return $conveyancer;
    }
}
