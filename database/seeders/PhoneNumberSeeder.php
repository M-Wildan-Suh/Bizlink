<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PhoneNumber;

class PhoneNumberSeeder extends Seeder
{
    public function run(): void
    {
        $numbers = [
            ['no_tlp' => '6281234567890', 'chat' => 'Halo, saya ingin bertanya tentang properti ini.'],
            ['no_tlp' => '6289876543210', 'chat' => 'Tanya dong min.'],
            ['no_tlp' => '6285555555555', 'chat' => 'Hubungi saya segera.'],
        ];

        foreach ($numbers as $number) {
            PhoneNumber::updateOrCreate(['no_tlp' => $number['no_tlp']], $number);
        }
    }
}
