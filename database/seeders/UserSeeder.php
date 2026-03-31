<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@bizlink.sites.id'],
            [
                'name' => 'Bizlink Admin',
                'slug' => 'bizlink-admin',
                'role' => 'superadmin',
                'email_verified_at' => now(),
                'password' => Hash::make('12345678'),
            ],
        );

        $users = [
            [
                'name' => 'Bizlink Editor',
                'slug' => 'bizlink-editor',
                'email' => 'editor@bizlink.sites.id',
                'role' => 'user',
            ],
            [
                'name' => 'Bizlink Writer',
                'slug' => 'bizlink-writer',
                'email' => 'writer@bizlink.sites.id',
                'role' => 'user',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'slug' => $user['slug'],
                    'role' => $user['role'],
                    'email_verified_at' => now(),
                    'password' => Hash::make('12345678'),
                ],
            );
        }
    }
}
