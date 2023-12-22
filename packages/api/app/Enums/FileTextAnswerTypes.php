<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class FileTextAnswerTypes extends Enum
{
    const Enclosed = 'enclosed';

    const Attached = 'attached';

    const AddLater = 'add_later';

    const NotApplicable = 'not_applicable';
}
