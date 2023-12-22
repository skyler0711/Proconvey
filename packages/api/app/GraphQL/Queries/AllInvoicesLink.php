<?php

namespace App\GraphQL\Queries;

use App\MediaUrlGenerator;
use App\Models\Conveyancer;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

final class AllInvoicesLink
{
    /**
     * @param  Conveyancer  $_
     * @param  array{}  $args
     */
    public function __invoke(Conveyancer $conveyancer, array $args)
    {
        $invoices = collect($conveyancer->getInvoices());

        $filename = tempnam(sys_get_temp_dir(), "invoices-$conveyancer->id.pdf");

        $zip = new ZipArchive();
        $zip->open($filename, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach ($invoices as $invoice) {
            if (! $invoice->pdf_url) {
                continue;
            }
            $zip->addFromString("Invoice-$invoice->number.pdf", Http::get($invoice->pdf_url)->body());
        }

        $zip->close();

        $customerNumber = explode('-', $invoices->first(fn ($i) => $i->number)->number)[0];

        $newFile = Storage::putFileAs('/tmp', $filename, "invoices-$customerNumber.zip");

        return MediaUrlGenerator::getManualUrl($newFile);
    }
}
