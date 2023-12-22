<?php

namespace App\Helpers;

use App\Models\Form;
use App\Notifications\EnquiryFormNotification;
use App\Notifications\GettingStartedMortgagesAndRelatedTransactionsNotification;
use App\Notifications\GettingStartedTheOwnersCompanyNotification;
use App\Notifications\GettingStartedTheOwnersNotification;
use App\Notifications\TA10FittingsAndContentNotification;
use App\Notifications\TA6PropertyInformationNotification;
use App\Notifications\TA7LeaseholdInformationNotification;
use App\Notifications\TA9CommonholdInformationNotification;

class NotificationHelper
{
    public static function sendNotification(Form $form)
    {
        $user = auth()->user();

        $user->with('notificationPreferences')->where('id', $user->id)->first()->notify(
            match ($form->order_number) {
                1 => new GettingStartedTheOwnersNotification(),
                2 => new GettingStartedMortgagesAndRelatedTransactionsNotification(),
                3 => new GettingStartedTheOwnersCompanyNotification(),
                4 => new TA6PropertyInformationNotification(),
                5 => new TA7LeaseholdInformationNotification(),
                6 => new TA9CommonholdInformationNotification(),
                7 => new TA10FittingsAndContentNotification(),
                8 => new EnquiryFormNotification(),
            }
        );
    }
}
