<?php

namespace App\GraphQL\Queries;

use App\MediaUrlGenerator;
use App\Models\Conveyancer;
use App\Models\Property;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

final class AllDocumentsLink
{
    /**
     * @param  Conveyancer  $_
     */
    public function __invoke(Property $property)
    {
        $documents = collect($property->getDocumentsAttribute());

        if ($documents->isEmpty()) {
            return null;
        }

        $filename = tempnam(sys_get_temp_dir(), 'documents');

        $zip = new ZipArchive();

        $zip->open($filename, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach ($documents as $document) {
            $name = $document->name;
            $zip->addFromString("$name.pdf", Storage::get($document->getPathRelativeToRoot()));
        }

        $zip->close();

        $newFile = Storage::putFileAs('/tmp', $filename, "$property->case_reference-pack.zip");

        return MediaUrlGenerator::getManualUrl($newFile);
    }
}
