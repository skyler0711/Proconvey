<?php

namespace App\GraphQL\Mutations\PropertyPack;

use App\Enums\DocumentType;
use App\Models\Property;

final class UploadSofCheckDocuments
{
    /**
     * @param  null  $_
     * @param  array{}  $args
     */
    public function __invoke($_, array $args)
    {
        /** @var \App\Models\User */
        $user = auth()->user();

        /** @var \App\Models\Property */
        $property = Property::find($args['property_id']);

        $newDocs = collect();

        if (isset($args['input'])) {
            $property->clearMediaCollection('documents');

            foreach ($args['input']['documents'] as $index => $document) {
                $newDocs[] = $property
                    ->addMediaFromDisk($document['key'])
                    ->usingFileName(explode('/', $document['key'])[1].'.'.$document['extension'])
                    ->withCustomProperties([
                        'type' => DocumentType::SofCheck,
                    ])
                    ->usingName("{$user->full_name} Source of Funds Check {$index}")
                    ->toMediaCollection('documents');
            }

            if ($newDocs->count() > 0) {
                $property->users()->updateExistingPivot($user->id, [
                    'sof_completed_at' => now(),
                ]);
            }
        }

        $property->save();

        return $newDocs->map(function ($doc) {
            return [
                'name' => $doc->mediaName,
                'id' => $doc->id,
            ];
        });
    }
}
