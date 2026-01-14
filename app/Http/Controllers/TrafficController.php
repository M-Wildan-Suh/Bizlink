<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\ArticleShow;
use App\Models\GuardianWeb;
use App\Models\Traffic;
use Illuminate\Http\Request;

class TrafficController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $mode = $request->query('mode', 'day'); // default day
        $list = $request->query('list', 'guardian'); // default day

        if ($mode === 'day') {
            $traffic = $this->trafficDay();

            $start = now()->subHours(24);
            $end   = now();
        } elseif ($mode === 'week') {
            $traffic = $this->trafficWeek();

            $start = now()->subDays(7);
            $end   = now();
        } elseif ($mode === 'month') {
            $traffic = $this->trafficMonth();

            $end   = now();
            $start = now()->subDays(30);
        } else {
            $traffic = [
                'labels' => [],
                'values' => [],
                'articleIds' => [],
            ];
        }

        $guardians = [];
        $categories = [];
        $articles = [];

        if ($list === 'guardian') {
            $guardians = GuardianWeb::whereHas('articles.articleshow', function ($q) use ($traffic) {
                $q->whereIn('id', $traffic['articleIds']);
            })
                ->with('articles.articleshow')
                ->simplePaginate(10);

            $guardians =  $guardians->map(function ($guardian) use ($traffic, $start, $end) {

                // Ambil semua article_show_id milik guardian
                $ids = $guardian->articles
                    ->flatMap(fn($a) => $a->articleshow->pluck('id'))
                    ->filter(fn($id) => in_array($id, $traffic['articleIds']))
                    ->values()
                    ->toArray();

                // Hitung access-nya
                $query = Traffic::whereIn('article_show_id', $ids);

                if ($start && $end) {
                    $query->whereBetween('created_at', [$start, $end]);
                }

                // Tambah field access
                $guardian->access = $query->sum('access');

                return $guardian;
            });

            $noGuardianArticleShowIds = Article::whereNull('guardian_web_id')
                ->with('articleshow')
                ->get()
                ->flatMap(fn($a) => $a->articleshow->pluck('id'))
                ->filter(fn($id) => in_array($id, $traffic['articleIds'])) // ikut mode
                ->values()
                ->toArray();

            $noGuardianAccessQuery = Traffic::whereIn('article_show_id', $noGuardianArticleShowIds)->whereBetween('created_at', [$start, $end]); 

            $noGuardianAccess = $noGuardianAccessQuery->sum('access');

            if ($noGuardianAccess > 0) {
                $guardians->push((object)[
                    'id' => null,
                    'url' => 'bizlink.sites.id',
                    'access' => $noGuardianAccess,
                ]);
            }

            $guardians = $guardians
                ->sortByDesc('access')
                ->values();
        } elseif ($list === 'category') {
            $categories = ArticleCategory::whereHas('articles.articleshow', function ($q) use ($traffic) {
                $q->whereIn('id', $traffic['articleIds']);
            })
                ->with('articles.articleshow')
                ->simplePaginate(10);

            $categories = $categories->map(function ($category) use ($traffic, $start, $end) {

                // Ambil semua article_show_id milik category
                $ids = $category->articles
                    ->flatMap(fn($a) => $a->articleshow->pluck('id'))
                    ->filter(fn($id) => in_array($id, $traffic['articleIds']))
                    ->values()
                    ->toArray();

                // Hitung access
                $query = Traffic::whereIn('article_show_id', $ids);

                if ($start && $end) {
                    $query->whereBetween('created_at', [$start, $end]);
                }

                // Tambah field access
                $category->access = $query->sum('access');

                return $category;
            });

            $categories = $categories
                ->sortByDesc('access')
                ->values();
        } elseif ($list === 'article') {
            $articles = ArticleShow::whereIn('id', $traffic['articleIds'])->simplePaginate(10);

            $articles = $articles->map(function ($article) use ($start, $end) {
                $article->access = Traffic::where('article_show_id', $article->id)
                    ->whereBetween('created_at', [$start, $end])
                    ->sum('access');

                return $article;
            });

            $articles = $articles
                ->sortByDesc('access')
                ->values();
        }

        $totalaccess = Traffic::whereBetween('created_at', [$start, $end])
            ->sum('access');

        return view('admin.traffic.index', compact('traffic', 'mode', 'list', 'guardians', 'articles', 'categories', 'totalaccess'));
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



    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Traffic $traffic)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Traffic $traffic)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Traffic $traffic)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Traffic $traffic)
    {
        //
    }
}
