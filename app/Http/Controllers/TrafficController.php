<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\ArticleShow;
use App\Models\GuardianWeb;
use App\Models\Traffic;
use Carbon\Carbon;
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

            $start = Carbon::parse($request->query('start', now()->subHours(23)))->startOfHour();

            $end = Carbon::parse($request->query('end', now()))->startOfHour();

            $traffic = $this->trafficDay($start, $end);
        } elseif ($mode === 'week') {

            $start = Carbon::parse($request->query('start', now()->subDays(6)))->startOfDay();

            $end = Carbon::parse($request->query('end', now()))->endOfDay();

            $traffic = $this->trafficWeek($start, $end);
        } elseif ($mode === 'month') {

            $start = Carbon::parse($request->query('start', now()->subDays(29)))->startOfDay();

            $end = Carbon::parse($request->query('end', now()))->endOfDay();

            $traffic = $this->trafficMonth($start, $end);
        } else {

            $traffic = [
                'labels'     => [],
                'values'     => [],
                'articleIds' => [],
            ];
        }

        $guardians = [];
        $categories = [];
        $articles = [];

        if ($list === 'guardian') {
            $guardians = GuardianWeb::withSum(
                ['traffic as access' => function ($q) use ($start, $end) {
                    if ($start && $end) {
                        $q->whereBetween('created_at', [$start, $end]);
                    }
                }],
                'access'
            )
                ->orderByDesc('access')
                ->simplePaginate(10);

            // hitung no guardian
            if (!$request->ajax()) {
                $noGuardianAccess = Traffic::whereNull('guardian_web_id')
                    ->whereIn('article_show_id', $traffic['articleShowIds'])
                    ->whereBetween('created_at', [$start, $end])
                    ->sum('access');

                if ($noGuardianAccess > 0) {

                    // ambil collection paginator
                    $items = $guardians->getCollection();

                    // gabungkan item tambahan
                    $items->push((object)[
                        'id' => null,
                        'url' => 'bizlink.sites.id',
                        'access' => $noGuardianAccess,
                    ]);

                    // 🔥 SORT ULANG BERDASARKAN ACCESS
                    $items = $items->sortByDesc('access')->values();

                    // set kembali ke paginator
                    $guardians->setCollection($items);
                }
            }
        } elseif ($list === 'category') {
            $categories = ArticleCategory::query()
                ->withCount('articles')
                ->addSelect([
                    'total_access' => Traffic::selectRaw('COALESCE(SUM(access),0)')
                        ->join('articles', 'articles.id', '=', 'traffic.article_id')
                        ->join('pivot_articles_categories as pac', 'pac.article_id', '=', 'articles.id')
                        ->whereColumn('pac.category_id', 'article_categories.id')
                        ->whereBetween('traffic.created_at', [$start, $end])
                ])
                ->simplePaginate(10);
        } elseif ($list === 'article') {
            $articles = ArticleShow::withSum(
                ['traffic as access' => function ($q) use ($start, $end) {
                    $q->whereBetween('created_at', [$start, $end]);
                }],
                'access'
            )
                ->whereIn('id', $traffic['articleShowIds'])
                ->orderByDesc('access')
                ->simplePaginate(10);
        }

        if ($request->ajax()) {
            return view('admin.traffic.row', compact('guardians', 'articles', 'categories', 'list'))->render();
        }

        $totalaccess = Traffic::whereBetween('created_at', [$start, $end])
            ->sum('access');

        return view('admin.traffic.index', compact('traffic', 'mode', 'list', 'guardians', 'articles', 'categories', 'totalaccess', 'start', 'end'));
    }

    private function trafficDay($start, $end)
    {
        // 1️⃣ Ambil traffic per jam (SUM access)
        $traffic = Traffic::selectRaw('HOUR(created_at) as hour, SUM(access) as total')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('hour')
            ->pluck('total', 'hour');

        // 2️⃣ Ambil semua article_show_id sekali
        $ids = Traffic::whereBetween('created_at', [$start, $end])
            ->get(['article_show_id', 'article_id', 'guardian_web_id']);

        $articleShowIds = $ids->pluck('article_show_id')->unique()->values()->toArray();
        $articleIds = $ids->pluck('article_id')->unique()->values()->toArray();
        $guardianIds = $ids->pluck('guardian_web_id')->unique()->values()->toArray();


        $labels = [];
        $values = [];

        for ($time = $start->copy(); $time <= $end; $time->addHour()) {
            $hour = (int) $time->format('H');

            $labels[] = $time->format('H:00');
            $values[] = $traffic[$hour] ?? 0;
        }

        return [
            'labels' => $labels,
            'values' => $values,
            'articleIds' => $articleIds,
            'articleShowIds' => $articleShowIds,
            'guardianWebIds' => $guardianIds,
        ];
    }


    private function trafficWeek($start, $end)
    {

        // 1️⃣ Ambil total access per hari
        $traffic = Traffic::selectRaw('DATE(created_at) as date, SUM(access) as total')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('date')
            ->pluck('total', 'date');

        // 2️⃣ Ambil semua article_show_id sekali
        $ids = Traffic::whereBetween('created_at', [$start, $end])
            ->get(['article_show_id', 'article_id', 'guardian_web_id']);

        $articleShowIds = $ids->pluck('article_show_id')->unique()->values()->toArray();
        $articleIds = $ids->pluck('article_id')->unique()->values()->toArray();
        $guardianIds = $ids->pluck('guardian_web_id')->unique()->values()->toArray();

        $labels = [];
        $values = [];

        for ($day = $start->copy(); $day <= $end; $day->addDay()) {
            $dateKey = $day->toDateString();

            $labels[] = $day->format('D d');
            $values[] = $traffic[$dateKey] ?? 0;
        }

        return [
            'labels' => $labels,
            'values' => $values,
            'articleIds' => $articleIds,
            'articleShowIds' => $articleShowIds,
            'guardianWebIds' => $guardianIds,
        ];
    }


    private function trafficMonth($start, $end)
    {

        // 1️⃣ Ambil total access per hari
        $traffic = Traffic::selectRaw('DATE(created_at) as date, SUM(access) as total')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('date')
            ->pluck('total', 'date');

        // 2️⃣ Ambil semua article_show_id sekali
        $ids = Traffic::whereBetween('created_at', [$start, $end])
            ->get(['article_show_id', 'article_id', 'guardian_web_id']);

        $articleShowIds = $ids->pluck('article_show_id')->unique()->values()->toArray();
        $articleIds = $ids->pluck('article_id')->unique()->values()->toArray();
        $guardianIds = $ids->pluck('guardian_web_id')->unique()->values()->toArray();

        $labels = [];
        $values = [];

        for ($day = $start->copy(); $day <= $end; $day->addDay()) {
            $dateKey = $day->toDateString();

            $labels[] = $day->format('d M');
            $values[] = $traffic[$dateKey] ?? 0;
        }

        return [
            'labels' => $labels,
            'values' => $values,
            'articleIds' => $articleIds,
            'articleShowIds' => $articleShowIds,
            'guardianWebIds' => $guardianIds,
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
