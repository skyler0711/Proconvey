<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class UserJobRole extends Enum
{
    const Conveyancer = 'conveyancer';

    const Paralegal = 'paralegal';

    const Assistant = 'assistant';

    const Other = 'other';
}
