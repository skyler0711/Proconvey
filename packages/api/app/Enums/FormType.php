<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

final class FormType extends Enum
{
    const GettingStarted = 'getting_started';

    const GettingStartedMortgages = 'getting_started_mortgages';

    const Individual = 'individual';

    const Company = 'company';

    const Giftor = 'giftor';

    const TA6PropertyInformation = 'ta6_property_information';

    const TA7LeaseholdInformation = 'ta7_leasehold_information';

    const TA9CommonholdInformation = 'ta9_commonhold_information';

    const TA10FittingsAndContents = 'ta10_fittings_and_contents';
}
