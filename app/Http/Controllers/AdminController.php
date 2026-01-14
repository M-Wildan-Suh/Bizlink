<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\ArticleShow;
use App\Models\GuardianWeb;
use App\Models\SourceCode;
use App\Models\Traffic;
use Carbon\Carbon;
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

    public function dashboard(Request $request)
    {
        $data = GuardianWeb::all();

        $data->transform(function ($data) {
            $data->spintaxcount = $this->formatCount($data->articles->where('article_type', 'spintax')->count());

            $data->spincount = $this->formatCount(ArticleShow::whereHas('articles', function ($query) use ($data) {
                $query->where('guardian_web_id', $data->id)
                    ->where('article_type', 'spintax');
            })->count());

            $data->categories = ArticleCategory::whereHas('articles', function ($query) use ($data) {
                $query->where('guardian_web_id', $data->id);
            })->select(['category', 'slug'])->get();

            $data->uniquecount = $this->formatCount($data->articles->where('article_type', 'unique')->count());
            return $data;
        });

        // Manual website
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

        // Traffic charts
        $mode = $request->query('mode', 'day'); // default day

        if ($mode === 'day') {
            $traffic = $this->trafficDay();
        } elseif ($mode === 'week') {
            $traffic = $this->trafficWeek();
        } elseif ($mode === 'month') {
            $traffic = $this->trafficMonth();
        } else {
            $traffic = [
                'labels' => [],
                'values' => []
            ];
        }

        // Other dashboard data
        $guardian = $this->formatCount(GuardianWeb::all()->count());
        $sc = $this->formatCount(SourceCode::all()->count());
        $spintax = $this->formatCount(Article::where('article_type', 'spintax')->count());
        $spin = $this->formatCount(ArticleShow::whereHas('articles', function ($query) {
            $query->where('article_type', 'spintax');
        })->count());
        $unique = $this->formatCount(Article::where('article_type', 'unique')->count());

        return view('dashboard', compact('data', 'sc', 'spintax', 'spin', 'unique', 'guardian', 'traffic', 'mode'));
    }


    private function trafficDay()
    {
        $labels = [];
        $values = [];
        $articleIds = [];

        $start = now()->subHours(23)->startOfHour();
        $end   = now()->startOfHour();

        for ($time = $start->copy(); $time <= $end; $time->addHour()) {
            $labels[] = $time->format('H:00');

            $query = Traffic::whereBetween('created_at', [
                $time,
                $time->copy()->endOfHour()
            ]);

            $values[] = $query->sum('access');
            $articleIds = array_merge(
                $articleIds,
                $query->pluck('article_show_id')->toArray()
            );
        }

        return [
            'labels' => $labels,
            'values' => $values,
            'articleIds' => $articleIds,
        ];
    }

    private function trafficWeek()
    {
        $labels = [];
        $values = [];
        $articleIds = [];

        $start = now()->subDays(6)->startOfDay();
        $end   = now()->endOfDay();

        for ($day = $start->copy(); $day <= $end; $day->addDay()) {
            $labels[] = $day->format('D d');

            $query = Traffic::whereBetween('created_at', [
                $day->copy()->startOfDay(),
                $day->copy()->endOfDay()
            ]);

            $values[] = $query->sum('access');
            $articleIds = array_merge(
                $articleIds,
                $query->pluck('article_show_id')->toArray()
            );
        }

        return [
            'labels' => $labels,
            'values' => $values,
            'articleIds' => $articleIds,
        ];
    }

    private function trafficMonth()
    {
        $labels = [];
        $values = [];
        $articleIds = [];

        $start = now()->subDays(29)->startOfDay();
        $end   = now()->endOfDay();

        for ($day = $start->copy(); $day <= $end; $day->addDay()) {
            $labels[] = $day->format('d M');

            $query = Traffic::whereBetween('created_at', [
                $day->copy()->startOfDay(),
                $day->copy()->endOfDay()
            ]);

            $values[] = $query->sum('access');
            $articleIds = array_merge(
                $articleIds,
                $query->pluck('article_show_id')->toArray()
            );
        }

        return [
            'labels' => $labels,
            'values' => $values,
            'articleIds' => $articleIds,
        ];
    }
}
