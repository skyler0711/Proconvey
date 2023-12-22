<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class PropertyUserRole extends Enum
{
    const Owner = 'owner';

    const Buyer = 'buyer';

    const Remortgager = 'remortgager';

    const Attorney = 'attorney';

    const Deputy = 'deputy';

    const Executor = 'executor';

    const Giftor = 'giftor';
}
