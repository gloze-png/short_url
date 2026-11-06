<?php

namespace Tests\Feature;

use App\Models\ShortUrl;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShortUrlTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_guest_can_create_a_short_link(): void
    {
        $response = $this->post('/urls', [
            'original_url' => 'https://laravel.com/docs',
        ]);

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('short_url');

        $this->assertDatabaseHas('short_urls', [
            'original_url' => 'https://laravel.com/docs',
            'user_id'      => null,
        ]);

        $this->assertSame(6, strlen(ShortUrl::first()->short_code));
    }

    public function test_short_link_redirects_and_logs_a_click(): void
    {
        $url = ShortUrl::create([
            'original_url' => 'https://example.com',
            'short_code'   => 'abc123',
        ]);

        $response = $this
            ->withHeaders([
                'User-Agent' => 'TestBrowser/1.0',
                'Referer'    => 'https://google.com',
            ])
            ->get('/s/abc123');

        $response->assertRedirect('https://example.com');

        $this->assertDatabaseHas('url_clicks', [
            'short_url_id' => $url->id,
            'user_agent'   => 'TestBrowser/1.0',
            'referer'      => 'https://google.com',
        ]);
    }

    public function test_expired_link_returns_410_and_logs_no_click(): void
    {
        ShortUrl::create([
            'original_url' => 'https://example.com',
            'short_code'   => 'old123',
            'expires_at'   => now()->subDay(),
        ]);

        $this->get('/s/old123')->assertStatus(410);

        $this->assertDatabaseCount('url_clicks', 0);
    }
}