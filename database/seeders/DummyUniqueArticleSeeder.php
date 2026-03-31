<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\ArticleShow;
use App\Models\ArticleTag;
use App\Models\PhoneNumber;
use App\Models\Template;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DummyUniqueArticleSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $user = User::query()->firstOrCreate(
                ['slug' => 'dummy-article-writer'],
                [
                    'email' => 'dummy-article@bizlink.sites.id',
                    'name' => 'Dummy Article Writer',
                    'role' => 'user',
                    'password' => bcrypt('12345678'),
                    'email_verified_at' => now(),
                ]
            );

            $template = Template::query()->firstOrCreate(
                ['name' => 'Default Dummy Template'],
                [
                    'image' => null,
                    'bg_type' => 'normal',
                    'bg_image' => null,
                    'bg_main_color' => '#f8fafc',
                    'bg_second_color' => null,
                    'head_type' => 'one',
                    'gallery_type' => 'one',
                    'desc_text_color' => '#111827',
                    'desc_main_color' => '#ffffff',
                    'desc_second_color' => '#2563eb',
                    'contact_main_color' => '#1d4ed8',
                    'contact_second_color' => '#22c55e',
                ]
            );

            $phoneNumber = PhoneNumber::query()->firstOrCreate(
                ['no_tlp' => '+6281234567890'],
                ['chat' => 'Halo, saya tertarik dengan artikel dummy Bizlink.']
            );

            $category = ArticleCategory::query()->firstOrCreate(
                ['slug' => 'dummy-unique'],
                [
                    'category' => 'Dummy Unique',
                    'phone_number_id' => $phoneNumber->id,
                ]
            );

            $tag = ArticleTag::query()->firstOrCreate(
                ['slug' => 'dummy-article'],
                [
                    'tag' => 'Dummy Article',
                    'phone_number_id' => $phoneNumber->id,
                ]
            );

            $articles = [
                [
                    'title' => 'Dummy Article Unique 1',
                    'body' => '<p>Ini adalah artikel dummy unique pertama untuk kebutuhan pengujian tampilan dan alur data di Bizlink.</p><p>Kontennya dibuat sederhana supaya mudah dikenali saat testing.</p>',
                ],
                [
                    'title' => 'Dummy Article Unique 2',
                    'body' => '<p>Ini adalah artikel dummy unique kedua yang bisa dipakai untuk cek listing, detail artikel, dan relasi category atau tag.</p><p>Artikel ini memiliki slug yang berbeda dan aman untuk dijalankan berulang.</p>',
                ],
                [
                    'title' => 'Dummy Article Unique 3',
                    'body' => '<p>Ini adalah artikel dummy unique ketiga untuk melengkapi kebutuhan seeder sebanyak tiga data.</p><p>Gunakan data ini untuk QA, development, atau preview halaman article.</p>',
                ],
            ];

            foreach ($articles as $item) {
                $article = Article::query()->firstOrNew(['judul' => $item['title']]);
                $article->user_id = $user->id;
                $article->guardian_web_id = null;
                $article->article = $item['body'];
                $article->article_type = 'unique';
                $article->video_type = 'none';
                $article->youtube = null;
                $article->tiktok = null;
                $article->no_telephone = $phoneNumber->no_tlp;
                $article->no_whatsapp = $phoneNumber->no_tlp;
                $article->schedule = false;
                $article->save();

                $article->template()->sync([$template->id]);
                $article->articlecategory()->sync([$category->id]);
                $article->articletag()->sync([$tag->id]);

                ArticleShow::query()->updateOrCreate(
                    ['article_id' => $article->id],
                    [
                        'phone_number_id' => $phoneNumber->id,
                        'template_id' => $template->id,
                        'banner' => null,
                        'judul' => $item['title'],
                        'slug' => Str::slug($item['title']),
                        'article' => $item['body'],
                        'status' => 'publish',
                        'telephone' => true,
                        'whatsapp' => true,
                    ]
                );
            }
        });
    }
}
