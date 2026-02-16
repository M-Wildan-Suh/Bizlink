<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\ArticleShow;
use App\Models\GuardianWeb;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Response;

class GuardianWebController extends Controller
{
    public function export() {
        // Ambil semua data GuardianWeb dan ambil hanya field 'url'
        $urls = GuardianWeb::pluck('url');

        // Gabungkan URL menjadi string dengan newline
        $content = $urls->implode(PHP_EOL);

        // Siapkan response untuk download
        return Response::make($content, 200, [
            'Content-Type' => 'text/plain',
            'Content-Disposition' => 'attachment; filename="guardian_urls.txt"',
        ]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = GuardianWeb::select(['id', 'url', 'code'])
            ->withCount([
                'articles as spintaxcount' => function ($q) {
                    $q->where('article_type', 'spintax');
                },
                'articles as uniquecount' => function ($q) {
                    $q->where('article_type', 'unique');
                },
            ]);

        if ($request->search) {
            $query->where('url', 'like', '%' . $request->search . '%');
        }

        $data = $query->simplePaginate(20);

        $spinCountByGuardian = ArticleShow::query()
            ->join('articles', 'articles.id', '=', 'article_shows.article_id')
            ->where('articles.article_type', 'spintax')
            ->whereIn('articles.guardian_web_id', $data->pluck('id'))
            ->groupBy('articles.guardian_web_id')
            ->selectRaw('articles.guardian_web_id, COUNT(article_shows.id) as total')
            ->pluck('total', 'articles.guardian_web_id');

        $data->transform(function ($item) use ($spinCountByGuardian) {
            $item->spincount = (int) ($spinCountByGuardian[$item->id] ?? 0);
            $item->template = Cache::remember("guardian_template_{$item->id}", now()->addMinutes(15), function () use ($item) {
                try {
                    $response = Http::connectTimeout(2)->timeout(3)->get('https://' . $item->url . '/api/' . $item->code);
                    if ($response->successful()) {
                        return $response->json('template');
                    }
                } catch (\Exception $e) {
                    return null;
                }
                return null;
            });
            return $item;
        });
        
        if ($request->input('page', 1) == 1) {
            $manual = new \stdClass();
            $manual->id = -1;
            $manual->url = 'bizlink.sites.id';
            $manual->code = null;
            $manual->template = null;
            $manual->spintaxcount = Article::whereNull('guardian_web_id')->where('article_type', 'spintax')->count();
            $manual->spincount = ArticleShow::query()
                ->join('articles', 'articles.id', '=', 'article_shows.article_id')
                ->whereNull('articles.guardian_web_id')
                ->where('articles.article_type', 'spintax')
                ->count();
            $manual->uniquecount = Article::whereNull('guardian_web_id')->where('article_type', 'unique')->count();
    
            $data->prepend($manual);
        }

        if ($request->ajax()) {
            return view('admin.guardian.row', compact('data'))->render();
        }

        return view('admin.guardian.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $article = Article::whereNull('guardian_web_id')->get();

        return view('admin.guardian.create', compact('article'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'url' => 'required|max:255|unique:'.GuardianWeb::class,
            'article' => 'array',
        ]);

        $newguardian = new GuardianWeb;

        $newguardian->url = $request->url;
        $newguardian->code = strtoupper(Str::random(10));

        $newguardian->save();

        if ($request->has('article') && is_array($request->article)) {
            $newguardian->articles()->attach($request->article);
        }



        return redirect()->route('guardian.index');
    }

    /**
     * Display the specified resource.
     */
    public function show($id, GuardianWeb $guardianWeb)
    {
    $article = Article::whereNull('guardian_web_id')->orWhere('guardian_web_id', $id)->get();

        $guardianWeb = GuardianWeb::find($id);

        return view('admin.guardian.edit', compact('guardianWeb', 'article'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(GuardianWeb $guardianWeb)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update($id, Request $request, GuardianWeb $guardianWeb)
    {
        $validated = $request->validate([
            'url' => 'required|max:255|unique:' . GuardianWeb::class . ',url,' . $id,
            'article' => 'array',
        ]);

        $guardianWeb = GuardianWeb::find($id);
        
        $guardianWeb->url = $request->url;

        $guardianWeb->save();

        $old = Article::where('guardian_web_id', $guardianWeb->id)->get();
        foreach ($old as $item) {
            $item->guardian_Web_id = null;
            $item->save();
        }

        if ($request->article) {
            $articles = Article::whereIn('id', $request->article)->get();
            foreach ($articles as $item) {
                $item->guardian_Web_id = $guardianWeb->id;
                $item->save();
            }
        }
        
        return redirect()->route('guardian.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id, GuardianWeb $guardianWeb)
    {
        $guardianWeb = GuardianWeb::find($id);

        $guardianWeb->delete();

        return redirect()->back();
    }
}
