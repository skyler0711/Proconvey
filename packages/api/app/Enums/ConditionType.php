<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static AND()
 * @method static static OR()
 */
final class ConditionType extends Enum
{
    const AND = 'AND';

    const OR = 'OR';
}
