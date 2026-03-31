<?php

namespace Database\Seeders;

use App\Models\CpanelAccount;
use Illuminate\Database\Seeder;

class CpanelAccountSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $accounts = [
            [
                'name' => 'Guardian Hosting Alpha',
                'host' => 'cpanel-alpha.example.com',
                'port' => 2083,
                'username' => 'guardian_alpha',
                'api_token' => 'dummy-token-alpha-123456',
                'use_ssl' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Guardian Hosting Beta',
                'host' => 'cpanel-beta.example.com',
                'port' => 2083,
                'username' => 'guardian_beta',
                'api_token' => 'dummy-token-beta-123456',
                'use_ssl' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Guardian Hosting Gamma',
                'host' => 'cpanel-gamma.example.com',
                'port' => 2082,
                'username' => 'guardian_gamma',
                'api_token' => 'dummy-token-gamma-123456',
                'use_ssl' => false,
                'is_active' => false,
            ],
        ];

        foreach ($accounts as $account) {
            CpanelAccount::updateOrCreate(
                ['name' => $account['name']],
                $account,
            );
        }
    }
}
