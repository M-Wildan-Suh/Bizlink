<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Traffic;
use App\Models\WaTraffic;
use Carbon\Carbon;

class TrafficSeeder extends Seeder
{
    public function run(): void
    {
        $showIds = \App\Models\ArticleShow::pluck('id')->toArray();
        if (empty($showIds)) return;

        // Kosongkan data lama agar tidak menumpuk terlalu banyak
        Traffic::truncate();
        WaTraffic::truncate();

        // 30 hari terakhir * 24 jam
        for ($i = 0; $i < (30 * 24); $i++) {
            $time = Carbon::now()->subHours($i);
            $article_show_id = $showIds[array_rand($showIds)];
            
            // Random traffic
            Traffic::create([
                'article_show_id' => $article_show_id,
                'access' => rand(5, 50),
                'created_at' => $time,
                'updated_at' => $time,
            ]);

            // Random WA traffic
            WaTraffic::create([
                'article_show_id' => $article_show_id,
                'access' => rand(1, 20),
                'created_at' => $time,
                'updated_at' => $time,
            ]);
        }
    }
}
