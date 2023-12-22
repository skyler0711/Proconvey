<?php

namespace App\GraphQL\Mutations\PropertyPack;

use App\Enums\DocumentType;
use App\Models\Property;
use App\Models\User;
use App\Notifications\ClientDocumentNotification;

final class UploadAdditionalDocuments
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        /** @var \App\Models\User */
        $property = Property::find($args['property_id']);

        if (isset($args['input'])) {
            $property
                ->addMediaFromDisk($args['input']['uploaded_document']['key'])
                ->usingFileName(explode('/', $args['input']['uploaded_document']['key'])[1].'.'.$args['input']['uploaded_document']['extension'])
                ->withCustomProperties([
                    'type' => DocumentType::Additional,
                ])
                ->usingName($args['input']['name'])
                ->toMediaCollection('documents');
        }

        $property->save();
        $current_user = auth()->user();
        $conveyancers = User::with('notificationPreferences')->where('conveyancer_id', $property->conveyancer_id)->get();
        foreach ($conveyancers as $conveyancer) {
            if ($conveyancer->notificationPreferences->client_new_document_uploads) {
                $conveyancer->notify(new ClientDocumentNotification($conveyancer, $current_user));
            }
        }

        $mediaItem = $property->getMedia('documents')->last();
        $mediaItemId = $mediaItem->id;

        return [
            'name' => $args['input']['name'],
            'id' => $mediaItemId,
        ];
    }
}
