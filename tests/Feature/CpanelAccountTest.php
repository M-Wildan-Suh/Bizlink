<?php

namespace Tests\Feature;

use App\Models\CpanelAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CpanelAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_store_cpanel_account(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
        ]);

        $response = $this
            ->actingAs($admin)
            ->post(route('cpanel-account.store'), [
                'name' => 'Guardian Hosting 1',
                'host' => 'cpanel.example.com',
                'port' => 2083,
                'username' => 'guardian_user',
                'api_token' => 'test-token-123',
                'default_directory' => 'public_html/guardian',
                'use_ssl' => 1,
                'is_active' => 1,
            ]);

        $response->assertRedirect(route('cpanel-account.index'));

        $this->assertDatabaseHas('cpanel_accounts', [
            'name' => 'Guardian Hosting 1',
            'host' => 'cpanel.example.com',
            'port' => 2083,
            'username' => 'guardian_user',
            'default_directory' => 'public_html/guardian',
            'use_ssl' => 1,
            'is_active' => 1,
        ]);

        $account = CpanelAccount::first();

        $this->assertSame('test-token-123', $account->api_token);
        $this->assertNotSame('test-token-123', $account->getRawOriginal('api_token'));
    }
}
