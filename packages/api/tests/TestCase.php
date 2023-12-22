<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Nuwave\Lighthouse\Testing\RefreshesSchemaCache;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    // Wipes the database after each test
    use RefreshDatabase;

    // Provides $this->graphQL()
    use MakesGraphQLRequests;

    // Allows us to cache the GraphQL schema in advance to speed up testing
    use RefreshesSchemaCache;

    protected function setUp(): void
    {
        parent::setUp();
        $this->bootRefreshesSchemaCache();
    }

    protected function generateMockImage($fileName = 'photo.png', $width = 500)
    {
        Storage::fake();

        $file = UploadedFile::fake()->image($fileName, $width);

        $tempPath = Storage::putFile('tmp', $file);

        // Expected path after gone through a GraphQL mutation
        $filePath = Str::replace('tmp/', '', $tempPath).'.png';

        return [$tempPath, $filePath];
    }

    protected function generateMockPdf($fileName = 'document.pdf', $sizeInKilobytes = 10)
    {
        Storage::fake();

        $file = UploadedFile::fake()->create($fileName, $sizeInKilobytes);

        $tempPath = Storage::putFile('tmp', $file);

        // Expected path after gone through a GraphQL mutation
        $filePath = Str::replace('tmp/', '', $tempPath).'.pdf';

        return [$tempPath, $filePath];
    }
}
