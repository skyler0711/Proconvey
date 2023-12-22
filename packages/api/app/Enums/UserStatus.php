<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class UserStatus extends Enum
{
    const Active = 'active';

    const Archived = 'archived';

    const NotAccepted = 'invited';

    const OnboardingInProgress = 'onboarding';

    const PackInProgress = 'pack';

    const Complete = 'complete';

    const Sale = 'Sale';

    const Purchase = 'Purchase';

    const Remortgage = 'Remortgage';
}
