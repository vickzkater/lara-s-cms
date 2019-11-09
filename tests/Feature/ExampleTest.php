<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{
    public function test_login()
    {
        $response = $this->get(route('admin_login'));
        $response->assertStatus(200);
    }
}
