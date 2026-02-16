<?php

namespace App\Http\Controllers;

use App\Models\ArticleCategory;
use App\Models\ArticleShow;
use App\Models\ArticleTag;
use App\Models\User;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

class SitemapController extends Controller
{
    public function index()
    {
        $sitemap = Sitemap::create()
            ->add(Url::create('/')->setLastModificationDate(now()))
            ->add(Url::create('/artikel')->setLastModificationDate(now()));

        $perPage = 12;
        $baseQuery = ArticleShow::query()
            ->join('articles', 'articles.id', '=', 'article_shows.article_id')
            ->where('article_shows.status', 'publish')
            ->whereNull('articles.guardian_web_id');

        $totalArticles = (clone $baseQuery)->count('article_shows.id');
        $totalPages = ceil($totalArticles / $perPage);

        for ($page = 1; $page <= $totalPages; $page++) {
            $sitemap->add(
                Url::create("/artikel/page/{$page}")->setLastModificationDate(now())
            );
        }

        $userArticleCounts = (clone $baseQuery)
            ->whereNotNull('articles.user_id')
            ->groupBy('articles.user_id')
            ->selectRaw('articles.user_id, COUNT(article_shows.id) as total')
            ->pluck('total', 'articles.user_id');

        foreach (User::select(['id', 'slug', 'updated_at'])->get() as $model) {
            $slug = $model->slug;
            $baseUrl = "/penulis/{$slug}";
            $lastMod = $model->updated_at;

            $sitemap->add(Url::create($baseUrl)->setLastModificationDate($lastMod));

            $articleCount = (int) ($userArticleCounts[$model->id] ?? 0);
            $pages = ceil($articleCount / $perPage);

            for ($page = 1; $page <= $pages; $page++) {
                $sitemap->add(Url::create("{$baseUrl}/page/{$page}")->setLastModificationDate($lastMod));
            }
        }

        $categoryArticleCounts = (clone $baseQuery)
            ->join('pivot_articles_categories as pac', 'pac.article_id', '=', 'articles.id')
            ->groupBy('pac.category_id')
            ->selectRaw('pac.category_id, COUNT(article_shows.id) as total')
            ->pluck('total', 'pac.category_id');

        foreach (ArticleCategory::select(['id', 'slug', 'updated_at'])->get() as $model) {
            $slug = $model->slug;
            $baseUrl = "/kategori/{$slug}";
            $lastMod = $model->updated_at;

            $sitemap->add(Url::create($baseUrl)->setLastModificationDate($lastMod));

            $articleCount = (int) ($categoryArticleCounts[$model->id] ?? 0);
            $pages = ceil($articleCount / $perPage);

            for ($page = 1; $page <= $pages; $page++) {
                $sitemap->add(Url::create("{$baseUrl}/page/{$page}")->setLastModificationDate($lastMod));
            }
        }

        $tagArticleCounts = (clone $baseQuery)
            ->join('pivot_articles_tags as pat', 'pat.article_id', '=', 'articles.id')
            ->groupBy('pat.tag_id')
            ->selectRaw('pat.tag_id, COUNT(article_shows.id) as total')
            ->pluck('total', 'pat.tag_id');

        foreach (ArticleTag::select(['id', 'slug', 'updated_at'])->get() as $model) {
            $slug = $model->slug;
            $baseUrl = "/tag/{$slug}";
            $lastMod = $model->updated_at;

            $sitemap->add(Url::create($baseUrl)->setLastModificationDate($lastMod));

            $articleCount = (int) ($tagArticleCounts[$model->id] ?? 0);
            $pages = ceil($articleCount / $perPage);

            for ($page = 1; $page <= $pages; $page++) {
                $sitemap->add(Url::create("{$baseUrl}/page/{$page}")->setLastModificationDate($lastMod));
            }
        }

        ArticleShow::query()
            ->select(['id', 'slug', 'updated_at'])
            ->where('status', 'publish')
            ->whereHas('articles', function ($query) {
                $query->whereNull('guardian_web_id');
            })
            ->orderBy('id')
            ->chunkById(500, function ($models) use ($sitemap) {
                foreach ($models as $model) {
                    $sitemap->add(Url::create("/{$model->slug}")->setLastModificationDate($model->updated_at));
                }
            });

        $sitemap->writeToFile(public_path('sitemap.xml'));
        // return response()->download(public_path('sitemap.xml'));
        return redirect('/sitemap.xml');
    }
}
