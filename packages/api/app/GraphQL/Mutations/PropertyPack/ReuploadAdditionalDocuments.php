<?php

namespace App\GraphQL\Mutations\PropertyPack;

use App\Enums\DocumentType;
use App\Models\Property;
use App\Models\User;
use App\Notifications\ClientDocumentNotification;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

final class ReuploadAdditionalDocuments
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

        $media = Media::find($args['input']['file_id']);

        if ($media->model_type === 'property' && $property->id === $media->model_id) {
            $media->delete();
        }
        $current_user = auth()->user();
        $conveyancers = User::with('notificationPreferences')->where('conveyancer_id', $property->conveyancer_id)->get();
        foreach ($conveyancers as $conveyancer) {
            if ($conveyancer->notificationPreferences->client_new_document_uploads) {
                $conveyancer->notify(new ClientDocumentNotification($conveyancer, $current_user));
            }
        }
        $property->save();

        return [
            'name' => $args['input']['name'],
        ];
    }
}
