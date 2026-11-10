<?php

namespace Tests\Feature;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        Event::create([
            'title' => 'Test Event',
            'slug' => 'test-event',
            'access_password' => bcrypt('secret'),
            'is_active' => true,
        ]);

        $response = $this->get('/');
        $response->assertRedirect(route('attendance.form', ['slug' => 'test-event']));
    }
}
