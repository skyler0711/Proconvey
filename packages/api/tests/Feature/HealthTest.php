<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthTest extends TestCase
{
    public function testHealth(): void
    {
        $this
            ->get('/api/health')
            ->assertNoContent();
    }
}
