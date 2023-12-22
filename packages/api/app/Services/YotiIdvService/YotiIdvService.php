<?php

namespace App\Services\YotiIdvService;

use Illuminate\Support\Facades\App;
use Yoti\DocScan\Constants;
use Yoti\DocScan\DocScanClient;
use Yoti\DocScan\Session\Create\Check\RequestedDocumentAuthenticityCheckBuilder;
use Yoti\DocScan\Session\Create\Check\RequestedLivenessCheckBuilder;
use Yoti\DocScan\Session\Create\Check\RequestedThirdPartyIdentityCheckBuilder;
use Yoti\DocScan\Session\Create\Check\RequestedWatchlistScreeningCheckBuilder;
use Yoti\DocScan\Session\Create\Check\RequestedWatchlistScreeningConfigBuilder;
use Yoti\DocScan\Session\Create\CreateSessionResult;
use Yoti\DocScan\Session\Create\Filters\Document\DocumentRestrictionBuilder;
use Yoti\DocScan\Session\Create\Filters\Document\DocumentRestrictionsFilterBuilder;
use Yoti\DocScan\Session\Create\Filters\RequiredIdDocumentBuilder;
use Yoti\DocScan\Session\Create\Filters\RequiredSupplementaryDocumentBuilder;
use Yoti\DocScan\Session\Create\NotificationConfigBuilder;
use Yoti\DocScan\Session\Create\Objective\ProofOfAddressObjectiveBuilder;
use Yoti\DocScan\Session\Create\SdkConfigBuilder;
use Yoti\DocScan\Session\Create\SessionSpecification;
use Yoti\DocScan\Session\Create\SessionSpecificationBuilder;
use Yoti\DocScan\Session\Create\Task\RequestedTextExtractionTaskBuilder;
use Yoti\DocScan\Session\Retrieve\GetSessionResult;
use Yoti\Media\Media;
use Yoti\Sandbox\DocScan\Request\Check\Report\SandboxBreakdownBuilder;
use Yoti\Sandbox\DocScan\Request\Check\Report\SandboxRecommendationBuilder;
use Yoti\Sandbox\DocScan\Request\Check\SandboxDocumentAuthenticityCheckBuilder;
use Yoti\Sandbox\DocScan\Request\SandboxCheckReportsBuilder;
use Yoti\Sandbox\DocScan\Request\SandboxResponseConfigBuilder;
use Yoti\Sandbox\DocScan\Request\SandboxTaskResultsBuilder;
use Yoti\Sandbox\DocScan\Request\Task\SandboxDocumentTextDataExtractionTaskBuilder;
use Yoti\Sandbox\DocScan\SandboxClient;

final class YotiIdvService
{
    protected string $sdkId;

    protected string $pem;

    protected bool $sandbox;

    protected DocScanClient $client;

    protected SandboxClient $sandboxClient;

    public function __construct(string $sdkId, string $pem, bool $sandbox = false)
    {
        $this->sdkId = $sdkId;
        $this->pem = $pem;
        $this->sandbox = $sandbox;

        $this->client = new DocScanClient($this->sdkId, $this->pem, [
            'api.url' => $this->sandbox ? 'https://api.yoti.com/sandbox/idverify/v1' : 'https://api.yoti.com/idverify/v1',
        ]);

        if ($this->sandbox) {
            $this->sandboxClient = new SandboxClient($this->sdkId, $this->pem);
        }
    }

    protected function getSessionSpec(): SessionSpecification
    {
        $sessionBuilder = (new SessionSpecificationBuilder)

            // How long the client session is valid for
            ->withClientSessionTokenTtl(21600) // 6 hours

            // How long resources are valid for
            ->withResourcesTtl(108000) // Client session length + 24 hours

            // Check the authenticity of the document
            ->withRequestedCheck(
                (new RequestedDocumentAuthenticityCheckBuilder)
                    ->withManualCheckFallback()
                    ->build()
            )

            // Address check
            ->withRequestedCheck(
                (new RequestedThirdPartyIdentityCheckBuilder)
                    ->build()
            )

            // AML check
            ->withRequestedCheck(
                (new RequestedWatchlistScreeningCheckBuilder)
                    ->withConfig(
                        (new RequestedWatchlistScreeningConfigBuilder)
                            ->withSanctionsCategory()
                            ->build()
                    )
                    ->build(),
            )

            // Liveness check
            ->withRequestedCheck(
                (new RequestedLivenessCheckBuilder)
                    ->forZoomLiveness()
                    ->withMaxRetries(3)
                    ->build()
            )

            // Extract text from documents
            ->withRequestedTask(
                (new RequestedTextExtractionTaskBuilder)
                    ->withManualCheckFallback()
                    ->withChipDataDesired()
                    ->build()
            )

            // Restrict that ID documents must be from the UK
            ->withRequiredDocument(
                (new RequiredIdDocumentBuilder)
                    ->withFilter(
                        (new DocumentRestrictionsFilterBuilder)
                            ->withDocumentRestriction(
                                (new DocumentRestrictionBuilder)
                                    ->withCountries(['GBR'])
                                    ->build()
                            )
                            ->forWhitelist()
                            ->build()
                    )
                    ->build()
            )

            // Define the supporting document for address checks
            ->withRequiredDocument(
                (new RequiredSupplementaryDocumentBuilder)
                    ->withObjective(
                        (new ProofOfAddressObjectiveBuilder)
                            ->build()
                    )
                    ->withCountryCodes(['GBR'])
                    ->build()
            )

            // Require that the SDK uses our branding
            ->withSdkConfig(
                (new SdkConfigBuilder)
                    ->withPrimaryColour('#674186')
                    ->withSecondaryColour('#674186')
                    ->withPresetIssuingCountry('GBR')
                    ->build()
            );

        if (App::isProduction()) {
            $sessionBuilder = $sessionBuilder->withNotifications(
                (new NotificationConfigBuilder)
                    ->withEndpoint(config('app.url').'/webhooks/yotiidv')
                    ->forSessionCompletion()
                    ->build()
            );
        }

        $built = $sessionBuilder->build();
        logger(json_encode($built));

        return $built;
    }

    protected function configureSandboxSession(string $sessionId): void
    {
        $documentAuthenticityCheckConfig = (new SandboxDocumentAuthenticityCheckBuilder)
            ->withRecommendation(
                (new SandboxRecommendationBuilder)
                    ->withValue('APPROVE')
                    ->build()
            )
            ->withBreakdown(
                (new SandboxBreakdownBuilder)
                    ->withSubCheck('document_in_date')
                    ->withResult('PASS')
                    ->build()
            )
            ->build();

        $checkReportsConfig = (new SandboxCheckReportsBuilder)
            ->withDocumentAuthenticityCheck($documentAuthenticityCheckConfig)
            ->build();

        $textExtractionConfig = (new SandboxDocumentTextDataExtractionTaskBuilder)
            ->withDocumentFields([
                'full_name' => 'John Doe',
                'nationality' => 'GBR',
                'date_of_birth' => '1986-06-01',
                'document_number' => '123456789',
            ])
            ->build();

        $taskResultsConfig = (new SandboxTaskResultsBuilder)
            ->withDocumentTextDataExtractionTask($textExtractionConfig)
            ->build();

        $responseConfig = (new SandboxResponseConfigBuilder)
            ->withCheckReports($checkReportsConfig)
            ->withTaskResults($taskResultsConfig)
            ->build();

        $this->sandboxClient->configureSessionResponse($sessionId, $responseConfig);
    }

    public function createSession(): CreateSessionResult
    {
        $session = $this->client->createSession($this->getSessionSpec());

        if ($this->sandbox) {
            $this->configureSandboxSession($session->getSessionId());
        }

        return $session;
    }

    public function getSessionResult(string $sessionId): GetSessionResult
    {
        $sessionResult = $this->client->getSession($sessionId);

        return $sessionResult;
    }

    public function getMedia(string $sessionId, string $mediaId): Media
    {
        $media = $this->client->getMediaContent($sessionId, $mediaId);

        return $media;
    }

    public function recommendationToText(string $recommendation): string
    {
        return match ($recommendation) {
            'PASS' => 'Pass',
            'FAIL' => 'Fail',
            'NOT_AVAILABLE' => 'Not Available',
            'APPROVE' => 'Approve',
            'DONE' => 'Done',
            default => $recommendation,
        };
    }

    public function constantToText(string $constant): string
    {
        return match ($constant) {
            Constants::ID_DOCUMENT_AUTHENTICITY => 'Authenticity',
            Constants::ID_DOCUMENT_COMPARISON => 'Comparison',
            Constants::ID_DOCUMENT_TEXT_DATA_CHECK => 'Text Data',
            Constants::SUPPLEMENTARY_DOCUMENT_TEXT_DATA_CHECK => 'Supplementary Text Data',
            Constants::ID_DOCUMENT_FACE_MATCH => 'Face Match',
            Constants::THIRD_PARTY_IDENTITY => 'Identity',
            Constants::LIVENESS => 'Liveness',
            Constants::WATCHLIST_SCREENING => 'Watchlist Screening',
            Constants::WATCHLIST_ADVANCED_CA => 'Advanced Watchlist Screening',
            default => 'Unknown',
        };
    }

    public function breakdownToText(string $breakdown): string
    {
        return match ($breakdown) {
            'document_in_date' => 'Document in date',
            'fraud_list_check' => 'Fraud list check',
            'hologram' => 'Hologram',
            'hologram_movement' => 'Hologram movement',
            'mrz_validation' => 'MRZ validation',
            'no_sign_of_forgery' => 'No sign of forgery',
            'no_sign_of_tampering' => 'No sign of tampering',
            'ocr_mrz_comparison' => 'OCR MRZ comparison',
            'other_security_features' => 'Other security features',
            'physical_document_captured' => 'Physical document captured',
            'chip_csca_trusted' => 'Chip CSCA trusted',
            'chip_data_integrity' => 'Chip data integrity',
            'chip_digital_signature_verification' => 'Chip digital signature verification',
            'chip_parse' => 'Chip parse',
            'chip_sod_parse' => 'Chip SOD parse',
            'doc_number_validation' => 'Doc number validation',
            'age_estimation_dob_comparison' => 'Age estimation/DOB comparison',
            'fitness_probity' => 'Fitness probity',
            'pep' => 'PEP',
            'sanction' => 'Sanction',
            'warning' => 'Warning',
            'address_match' => 'Address match',
            'deceased' => 'Deceased',
            'dob_match' => 'DOB match',
            'electoral_roll' => 'Electoral roll',
            'name_match' => 'Name match',
            'pep_warning' => 'PEP warning',
            'liveness_auth' => 'Liveness auth',
            'provider_org' => 'Provider org',
            default => $breakdown,
        };
    }

    public function extractionToText(string $extraction): string
    {
        return match ($extraction) {
            'full_name' => 'Full name',
            'date_of_birth' => 'Date of birth',
            'nationality' => 'Nationality',
            'given_names' => 'Given names',
            'family_name' => 'Family name',
            'place_of_birth' => 'Place of birth',
            'gender' => 'Gender',
            'document_type' => 'Document type',
            'issuing_country' => 'Issuing country',
            'document_number' => 'Document number',
            'expiration_date' => 'Expiration date',
            'date_of_issue' => 'Date of issue',
            'issuing_authority' => 'Issuing authority',
            'mrz' => 'MRZ',
            'structured_postal_address' => 'Structured postal address',

            'type' => 'Type',
            'line1' => 'Line 1',
            'line2' => 'Line 2',

            'address_format' => 'Address format',
            'building_number' => 'Building number',
            'address_line1' => 'Address line 1',
            'address_line2' => 'Address line 2',
            'address_line3' => 'Address line 3',
            'town_city' => 'Town/City',
            'postal_code' => 'Postal code',
            'country_iso' => 'Country ISO',
            'country' => 'Country',
            'formatted_address' => 'Formatted address',

            default => $extraction,
        };
    }

    public function documentTypeToText(string $docType): string
    {
        return match ($docType) {
            'PASSPORT' => 'Passport',
            'DRIVING_LICENCE' => 'Driving licence',
            default => $docType,
        };
    }
}
