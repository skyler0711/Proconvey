<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class PaymentEventType extends Enum
{
    const ClientPack = 'client_pack';

    const IDV = 'idv';

    const ESig = 'esig';
}
