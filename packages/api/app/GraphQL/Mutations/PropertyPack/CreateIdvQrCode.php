<?php

namespace App\GraphQL\Mutations\PropertyPack;

use App\Enums\UserRole;
use App\Events\BillableAction;
use App\Models\IdVerification;
use App\Models\Property;
use App\Services\QrService\QrService;
use App\Services\YotiIdvService\YotiIdvService;

final class CreateIdvQrCode
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        /** @var QrService */
        $qrService = app()->make(QrService::class);

        /** @var YotiIdvService */
        $idvService = app()->make(YotiIdvService::class);

        if ($user->role !== UserRole::Client) {
            return null;
        }

        $property = Property::find($args['property_id']);

        if (! $property) {
            return null;
        }

        $idVerification = IdVerification::query()
            ->where('user_id', $user->id)
            ->where('conveyancer_id', $property->conveyancer_id)
            ->first();

        $idVerification->update([
            'mobile_connected_at' => null,
        ]);

        // Check the status of the session
        if ($idVerification->session_id) {
            $result = $idvService->getSessionResult($idVerification->session_id);
            if ($result->getState() === 'ONGOING') {
                return $qrService->generate($this->buildUri($idVerification->session_id, $idVerification->client_token));
            } elseif ($result->getState() === 'COMPLETE') {
                return null;
            }
        }

        // Create the session
        $session = $idvService->createSession();

        // Trigger the billable event
        event(new BillableAction($property));

        $idVerification->update([
            'session_id' => $session->getSessionId(),
            'client_token' => $session->getClientSessionToken(),
        ]);

        return $qrService->generate(
            $this->buildUri($session->getSessionId(), $session->getClientSessionToken())
        );
    }

    /**
     * Build the URI for the QR code.
     */
    protected function buildUri(string $sessionId, string $clientToken): string
    {
        return config('app.url').'/webhooks/mobile?'.http_build_query([
            'action' => 'idv',
            'session_id' => $sessionId,
            'client_token' => $clientToken,
        ]);
    }
}
