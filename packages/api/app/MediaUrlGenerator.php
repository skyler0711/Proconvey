<?php

namespace App;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\Support\UrlGenerator\DefaultUrlGenerator;

class MediaUrlGenerator extends DefaultUrlGenerator
{
    public function getUrl(): string
    {
        $disk = config('filesystems.disks.'.config('media-library.disk_name'));

        $url = App::isProduction()
            ? $this->getDisk()->temporaryUrl($this->getPathRelativeToRoot(), now()->addMinutes(5))
            : 'http://localhost:9000/'.$disk['bucket'].'/'.$this->getPathRelativeToRoot();

        return $this->versionUrl($url);
    }

    public static function getManualUrl(string $path): string
    {
        $diskName = config('filesystems.default');
        $disk = config('filesystems.disks.'.$diskName);

        return App::isProduction()
            ? Storage::disk($diskName)->temporaryUrl($path, now()->addMinutes(5))
            : 'http://localhost:9000/'.$disk['bucket'].'/'.$path;
    }
}
