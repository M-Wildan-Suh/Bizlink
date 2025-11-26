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

        for ($h = 0; $h < 24; $h++) {
            $labels[] = sprintf("%02d:00", $h);

            $values[] = Traffic::whereRaw('HOUR(created_at) = ?', [$h])
                ->whereDate('created_at', today())
                ->sum('access');
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }


    private function trafficWeek()
    {
        $labels = [];
        $values = [];

        $start = now()->startOfWeek();
        $end = now()->endOfWeek();

        for ($day = $start->copy(); $day <= $end; $day->addDay()) {
            $labels[] = $day->format('D d');

            $values[] = Traffic::whereDate('created_at', $day)
                ->sum('access');
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    private function trafficMonth()
    {
        $labels = [];
        $values = [];

        $start = now()->startOfMonth();
        $end = now()->endOfMonth();

        for ($day = $start->copy(); $day <= $end; $day->addDay()) {
            $labels[] = $day->format('d');

            $values[] = Traffic::whereDate('created_at', $day)
                ->sum('access');
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }
}
