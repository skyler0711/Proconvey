<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static fromFilename()
 */
final class DocumentType extends Enum
{
    const GiftorDeclaration = 'giftor_declaration';

    const ClientCareLetter = 'client_care_letter';

    const TermsAndConditions = 'terms_and_conditions';

    const ClientInformation = 'client_information';

    const ProtocolAndEnquiry = 'protocol_and_enquiry';

    const EvidenceProtocol = 'evidence_protocol';

    const EvidenceEnquiry = 'evidence_enquiry';

    const EvidenceGettingStarted = 'evidence_getting_started';

    const Additional = 'additional';

    const Idv = 'idv';

    const Form = 'form';

    const SofCheck = 'sof_check';

    const Unknown = 'unknown';
}

DocumentType::macro('fromFilename', function (string $filename) {
    if (preg_match('/.* Client Care Letter\.pdf$/', $filename) === 1) {
        return self::ClientCareLetter;
    }

    if (preg_match('/.* Terms & Conditions\.pdf$/', $filename) === 1) {
        return self::TermsAndConditions;
    }

    if (preg_match('/.* Giftor Declaration\.pdf$/', $filename) === 1) {
        return self::GiftorDeclaration;
    }

    return self::Unknown;
});
