<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\ArticleShow;
use App\Models\CpanelAccount;
use App\Models\GuardianWeb;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Client\Pool;
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
        $query = GuardianWeb::select(['id', 'url', 'code', 'use_cpanel', 'cpanel_account_id', 'cpanel_domain_created_at'])
            ->with(['cpanelAccount:id,name'])
            ->withCount([
                'articles as spintaxcount' => function ($q) {
                    $q->where('article_type', 'spintax');
                },
                'articles as uniquecount' => function ($q) {
                    $q->where('article_type', 'unique');
                },
            ])
            ->latest('id');

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
            $item->template = null;
            return $item;
        });

        $templates = [];
        $cacheKeys = [];

        foreach ($data as $item) {
            $key = "guardian_template_{$item->id}";
            $cacheKeys[$item->id] = $key;
            $templates[$item->id] = null;
        }

        if (!empty($cacheKeys)) {
            $cached = Cache::many(array_values($cacheKeys));

            foreach ($cacheKeys as $id => $key) {
                if (array_key_exists($key, $cached) && $cached[$key] !== null) {
                    $templates[$id] = $cached[$key];
                }
            }
        }

        // Untuk load more (AJAX), pakai cache saja agar response tidak menunggu HTTP eksternal per baris.
        if (!$request->ajax()) {
            $missing = $data->filter(function ($item) use ($templates) {
                return $templates[$item->id] === null;
            })->values();

            if ($missing->isNotEmpty()) {
                $responses = Http::pool(function (Pool $pool) use ($missing) {
                    $requests = [];

                    foreach ($missing as $item) {
                        $requests[] = $pool
                            ->as((string) $item->id)
                            ->connectTimeout(1)
                            ->timeout(2)
                            ->get('https://' . $item->url . '/api/' . $item->code);
                    }

                    return $requests;
                });

                foreach ($missing as $item) {
                    $id = (string) $item->id;
                    if (!isset($responses[$id])) {
                        continue;
                    }

                    try {
                        if ($responses[$id]->successful()) {
                            $value = $responses[$id]->json('template');
                            if ($value !== null) {
                                $templates[$item->id] = $value;
                                Cache::put($cacheKeys[$item->id], $value, now()->addMinutes(15));
                            }
                        }
                    } catch (\Throwable $e) {
                        // Biarkan null jika endpoint gagal / timeout.
                    }
                }
            }
        }

        $data->transform(function ($item) use ($templates) {
            $item->template = $templates[$item->id] ?? null;
            return $item;
        });
        
        if ($request->input('page', 1) == 1) {
            $manual = new \stdClass();
            $manual->id = -1;
            $manual->url = 'bizlink.sites.id';
            $manual->code = null;
            $manual->template = null;
            $manual->use_cpanel = false;
            $manual->cpanel_domain_created_at = null;
            $manual->cpanelAccount = null;
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
        $cpanelAccounts = CpanelAccount::where('is_active', true)->orderBy('name')->get();

        return view('admin.guardian.create', compact('article', 'cpanelAccounts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'url' => 'required|max:255|unique:'.GuardianWeb::class,
            'article' => 'array',
            'use_cpanel' => 'nullable|boolean',
            'cpanel_account_id' => 'nullable|required_if:use_cpanel,1|exists:cpanel_accounts,id',
            'cpanel_domain_type' => 'nullable|required_if:use_cpanel,1|in:subdomain,addon_domain',
        ]);

        $useCpanel = $request->boolean('use_cpanel');
        $newguardian = new GuardianWeb;

        $newguardian->url = $request->url;
        $newguardian->code = strtoupper(Str::random(10));
        $newguardian->use_cpanel = $useCpanel;
        $newguardian->cpanel_account_id = $useCpanel ? $request->cpanel_account_id : null;
        $newguardian->cpanel_domain_type = $useCpanel ? $request->cpanel_domain_type : null;
        $newguardian->cpanel_domain_created_at = null;

        $newguardian->save();

        if ($request->has('article') && is_array($request->article)) {
            Article::whereIn('id', $request->article)->update(['guardian_web_id' => $newguardian->id]);
        }

        return redirect()->route('guardian.index');
    }

    /**
     * Display the specified resource.
     */
    public function show($id, GuardianWeb $guardianWeb)
    {
        $article = Article::whereNull('guardian_web_id')->orWhere('guardian_web_id', $id)->get();
        $cpanelAccounts = CpanelAccount::where('is_active', true)
            ->orWhere('id', optional(GuardianWeb::find($id))->cpanel_account_id)
            ->orderBy('name')
            ->get();

        $guardianWeb = GuardianWeb::with('articles')->find($id);

        return view('admin.guardian.edit', compact('guardianWeb', 'article', 'cpanelAccounts'));
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
            'use_cpanel' => 'nullable|boolean',
            'cpanel_account_id' => 'nullable|required_if:use_cpanel,1|exists:cpanel_accounts,id',
            'cpanel_domain_type' => 'nullable|required_if:use_cpanel,1|in:subdomain,addon_domain',
        ]);

        $guardianWeb = GuardianWeb::find($id);
        $useCpanel = $request->boolean('use_cpanel');
        $domainConfigurationChanged =
            $guardianWeb->url !== $request->url ||
            (string) $guardianWeb->cpanel_account_id !== (string) $request->cpanel_account_id ||
            (string) $guardianWeb->cpanel_domain_type !== (string) $request->cpanel_domain_type;
        
        $guardianWeb->url = $request->url;
        $guardianWeb->use_cpanel = $useCpanel;
        $guardianWeb->cpanel_account_id = $useCpanel ? $request->cpanel_account_id : null;
        $guardianWeb->cpanel_domain_type = $useCpanel ? $request->cpanel_domain_type : null;
        $guardianWeb->cpanel_domain_created_at = $useCpanel
            ? ($domainConfigurationChanged ? null : $guardianWeb->cpanel_domain_created_at)
            : null;

        $guardianWeb->save();

        $old = Article::where('guardian_web_id', $guardianWeb->id)->get();
        foreach ($old as $item) {
            $item->guardian_web_id = null;
            $item->save();
        }

        if ($request->article) {
            $articles = Article::whereIn('id', $request->article)->get();
            foreach ($articles as $item) {
                $item->guardian_web_id = $guardianWeb->id;
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
