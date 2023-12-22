<?php

namespace App\Services\YotiSignService;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Http;
use stdClass;

final class YotiSignService
{
    protected bool $sandbox;

    protected string $baseUrl;

    protected string $key;

    protected $httpClient;

    public function __construct(
        string $key,
        bool $sandbox = false,
    ) {
        $this->key = $key;

        $this->sandbox = $sandbox;

        $this->baseUrl = $sandbox
            ? 'https://demo.api.yotisign.com'
            : 'https://api.yotisign.com';

        $this->httpClient = Http::withToken($this->key)->baseUrl($this->baseUrl);
    }

    /**
     * Get the branding for an envelope.
     */
    protected function getBranding(): array
    {
        return [
            'logo_options' => [
                'logo_choice' => 'brand_powered_by_yoti',
                'logo_base64' => base64_encode(file_get_contents(resource_path('img/logo.png'))),
            ],
            'primary_color' => '#674186',
            'primary_color_hover' => '#85679E',
            'on_primary_color' => '#FFFFFF',
            'secondary_color' => '#674186',
            'secondary_color_hover' => '#85679E',
        ];
    }

    /**
     * Create a new signing envelope.
     *
     * @param  string  $name The name of the envelope.
     * @param  YotiSignRecipient[]  $recipients The recipients of the envelope.
     * @param  YotiSignFile[]  $files The files for this envelope.
     */
    public function createEnvelope(string $name, array $recipients, array $files): array
    {
        $request = $this->httpClient;

        $options = [
            'name' => $name,
            'branding' => $this->getBranding(),
            'notifications' => App::isProduction()
                ? [
                    'destination' => config('app.url').'/webhooks/yotisign',
                    'subscriptions' => [
                        'envelope_completion',
                    ],
                ]
                : new stdClass,
            'sender' => [
                'event_notifications' => [],
            ],
            'recipients' => collect($recipients)->map(function (YotiSignRecipient $recipient) {
                return [
                    'name' => $recipient->getName(),
                    'email' => $recipient->getEmail(),
                    'auth_type' => 'no-auth',
                    'sign_group' => 1,
                    'event_notifications' => [],
                ];
            })->toArray(),
        ];

        foreach ($files as $file) {
            $fileName = $file->getName().'.pdf';
            $request->attach('file', $file->getPdf()->content, $fileName);

            if ($file->getSignature()) {
                foreach ($options['recipients'] as $index => $recipient) {
                    $options['recipients'][$index]['tags'][] = [
                        'page_number' => $file->getSignature()->getPageNumber(),
                        'type' => 'signature',
                        'x' => $file->getSignature()->getX(),
                        'y' => $file->getSignature()->getY(),
                        'optional' => false,
                        'file_name' => $fileName,
                    ];
                }
            }
        }

        $response = $request
            ->attach('options', json_encode($options))
            ->post('/v2/embedded-envelopes');

        if ($response->failed()) {
            throw new YotiSignException('Failed to create envelope: '.$response->body());
        }

        return $response->json();
    }

    /**
     * Get the embed url for a given envelope.
     *
     * @param  string  $token The token.
     */
    public function getEmbedUrl(string $token): string
    {
        return $this->sandbox
            ? "https://demo.www.yotisign.com/embedded/sign/{$token}"
            : "https://www.yotisign.com/embedded/sign/{$token}";
    }

    /**
     * Get the status of a given envelope.
     *
     * @param  string  $envelopeId The id of the envelope.
     */
    public function getEnvelopeStatus(string $envelopeId): string
    {
        $response = $this->httpClient
            ->get("/v2/envelopes/{$envelopeId}")
            ->json();

        return $response['status'];
    }

    /**
     * Get the completed documents for a given envelope.
     * Returns the string contents of a zip file.
     *
     * @param  string  $envelopeId The id of the envelope.
     */
    public function getCompletedDocuments(string $envelopeId): string
    {
        $response = $this->httpClient
            ->get("/v2/envelopes/{$envelopeId}/completed-documents")
            ->body();

        return $response;
    }
}
