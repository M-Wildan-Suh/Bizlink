<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\ArticleShow;
use App\Models\GuardianWeb;
use App\Models\SourceCode;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    private function formatCount($number)
    {
        if ($number >= 1000) {
            return round($number / 1000, 1) . 'k'; // contoh: 1500 → 1.5k
        }
        return (string) $number;
    }

    public function dashboard() {
        $data = GuardianWeb::all();

        $data->transform(function ($data) {
            $data->spintaxcount = $this->formatCount($data->articles->where('article_type', 'spintax')->count());

            $data->spincount = $this->formatCount(ArticleShow::whereHas('articles', function ($query) use ($data) {
                $query->where('guardian_web_id', $data->id)
                      ->where('article_type', 'spintax');
            })->count());

            $articleIds = $data->articles->pluck('id');
            $data->categories = ArticleCategory::whereHas('articles', function ($query) use ($data) {
                $query->where('guardian_web_id', $data->id);
            })->select(['category', 'slug'])->get();


            $data->uniquecount = $this->formatCount($data->articles->where('article_type', 'unique')->count());
            return $data;
        });

        $manual = new \stdClass();
        $manual->id = null;
        $manual->url = 'Main';

        $manual->categories = ArticleCategory::whereHas('articles', function ($query) {
                $query->whereNull('guardian_web_id');
            })->select(['category', 'slug'])->get();

        $manual->spintaxcount = $this->formatCount(Article::whereNull('guardian_web_id')->where('article_type', 'spintax')->count());
        $manual->spincount = $this->formatCount(ArticleShow::whereHas('articles', function ($query) {
            $query->whereNull('guardian_web_id')->where('article_type', 'spintax');
        })->count());
        $manual->uniquecount = $this->formatCount(Article::whereNull('guardian_web_id')->where('article_type', 'unique')->count());

        $data->prepend($manual);

        $guardian = $this->formatCount(GuardianWeb::all()->count());
        $sc = $this->formatCount(SourceCode::all()->count());
        $spintax = $this->formatCount(Article::where('article_type', 'spintax')->get()->count());
        $spin = $this->formatCount(ArticleShow::whereHas('articles', function ($query) {
            $query->where('article_type', 'spintax');
        })->count());
        $unique = $this->formatCount(Article::where('article_type', 'unique')->get()->count());
        return view('dashboard', compact('data', 'sc', 'spintax', 'spin', 'unique' ,'guardian'));
    }
}
