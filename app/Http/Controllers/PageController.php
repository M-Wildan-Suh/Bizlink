<?php

namespace App\Http\Controllers;

use App\Models\ArticleCategory;
use App\Models\ArticleShow;
use App\Models\ArticleTag;
use App\Models\PhoneNumber;
use App\Models\Template;
use App\Models\Traffic;
use App\Models\User;
use App\Models\WaTraffic;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;

class PageController extends Controller
{
    public function home(Request $request)
    {
        Paginator::currentPageResolver(function () use ($request) {
            return $request->route('page', 1); // default ke halaman 1
        });
        $data = ArticleShow::where('status', 'publish')
            ->whereHas('articles', function ($query) {
                $query->whereNull('guardian_web_id');
            })
            ->latest()->paginate(12);

        $category = ArticleCategory::whereHas('articles', function ($query) {
            $query->whereNull('guardian_web_id');
        })->get();

        $data->transform(function ($data) {
            $data->date = Carbon::parse($data->created_at)->locale('id')->translatedFormat('d F Y');
            $data->articles->articletag;
            $data->articles->user;
            return $data;
        });

        $trend = ArticleShow::orderBy('view', 'desc')
            ->where('status', 'publish')
            ->whereHas('articles', function ($query) {
                $query->whereNull('guardian_web_id');
            })
            ->take(6)->get();

        $data->withPath("/artikel/page");

        $hp = PhoneNumber::first()->no_tlp;

        return view('guest.home', compact('data', 'trend', 'category', 'hp'));
    }

    public function article(Request $request, $username = null, $category = null, $tag = null)
    {
        Paginator::currentPageResolver(function () use ($request) {
            return $request->route('page', 1);
        });

        $page = $request->route('page') ?? null;

        if ($username) {
            $data = ArticleShow::whereHas('articles.user', function ($query) use ($username) {
                $query->where('slug', $username);
            })
                ->whereHas('articles', function ($query) {
                    $query->whereNull('guardian_web_id');
                })
                ->where('status', 'publish')->latest()->paginate(12);

            $user = User::where('slug', $username)->first();

            $data->withPath("/penulis/{$user->slug}/page");

            $title = 'Penulis : ' . $user->name;
        } elseif ($category) {
            $data = ArticleShow::whereHas('articles.articleCategory', function ($query) use ($category) {
                $query->where('slug', $category);
            })
                ->whereHas('articles', function ($query) {
                    $query->whereNull('guardian_web_id');
                })
                ->where('status', 'publish')->latest()->paginate(12);

            $data->withPath("/kategori/{$category}/page");

            $category = ArticleCategory::where('slug', $category)->first()->category;
            $title = 'Kategori : ' . $category;
        } elseif ($tag) {
            $data = ArticleShow::whereHas('articles.articleTag', function ($query) use ($tag) {
                $query->where('slug', $tag);
            })
                ->whereHas('articles', function ($query) {
                    $query->whereNull('guardian_web_id');
                })
                ->where('status', 'publish')->latest()->paginate(12);

            $data->withPath("/tag/{$tag}/page");

            $tag = ArticleTag::where('slug', $tag)->first()->tag;
            $title = 'Tag : ' . $tag;
        } elseif ($request->search) {
            $data = ArticleShow::where(function ($query) use ($request) {
                $query->where('judul', 'like', '%' . $request->search . '%')
                    ->orWhereHas('articles.articleCategory', function ($q) use ($request) {
                        $q->where('category', 'like', '%' . $request->search . '%');
                    })
                    ->orWhereHas('articles.articleTag', function ($q) use ($request) {
                        $q->where('tag', 'like', '%' . $request->search . '%');
                    });
            })
                ->where('status', 'publish')
                ->whereHas('articles', function ($query) {
                    $query->whereNull('guardian_web_id');
                })->latest()->paginate(12);

            $data->withPath("/artikel/page");
            $title = 'Pecaharian : ' . $request->search;
        } else {
            $data = ArticleShow::where('status', 'publish')
                ->whereHas('articles', function ($query) {
                    $query->whereNull('guardian_web_id');
                })
                ->latest()->paginate(12);

            $data->withPath("/artikel/page");
            $title = 'Artikel Terbaru';
        }

        $data->transform(function ($data) {
            $data->date = Carbon::parse($data->created_at)->locale('id')->translatedFormat('d F Y');
            $data->articles->articletag;
            $data->articles->user;
            return $data;
        });

        $category = ArticleCategory::whereHas('articles', function ($query) {
            $query->whereNull('guardian_web_id');
        })->get();

        $hp = PhoneNumber::first()->no_tlp;

        return view('guest.article', compact('data', 'title', 'page', 'category', 'hp'));
    }

    public function business($slug)
    {
        $data = ArticleShow::where('slug', $slug)->first();

        if (!$data || $data->articles->guardian_web_id) {
            return redirect()->route('not.found');
        }

        // Tambah view di article_show
        $data->increment('view');

        $nowHour = Carbon::now()->format('Y-m-d H:00:00');

        // Cari traffic dalam jam yang sama
        $traffic = Traffic::where('article_show_id', $data->id)
            ->whereBetween('created_at', [
                Carbon::parse($nowHour),
                Carbon::parse($nowHour)->addHour()
            ])
            ->first();

        if ($traffic) {
            // Sudah ada record di jam ini → tambah access
            $traffic->increment('access');
        } else {
            // Belum ada record → buat baru
            Traffic::create([
                'article_show_id' => $data->id,
                'article_id' => $data->article_id,
                'guardian_web_id' => $data->article->guardian_web_id,
                'access' => 1,
            ]);
        }

        $template = $data->template;

        // dd($data->articles->phoneNumber);
        if ($data->phoneNumber) {
            $data->no_tlp = $data->phoneNumber->no_tlp;
        } elseif ($data->articles->articlecategory->first()?->phonenumber) {
            $data->no_tlp = $data->articles->articlecategory->first()->phoneNumber->no_tlp;
        } else {
            $data->no_tlp = optional(PhoneNumber::first())->no_tlp;
        }

        $data->date = Carbon::parse($data->created_at)->locale('id')->translatedFormat('d F Y');
        // dd($data->articles);

        $category = ArticleCategory::whereHas('articles', function ($query) {
            $query->whereNull('guardian_web_id');
        })->get();

        $hp = PhoneNumber::first()->no_tlp;

        return view('guest.business', compact('data', 'template', 'category', 'hp'));
    }

    public function notFound()
    {
        $category = ArticleCategory::whereHas('articles', function ($query) {
            $query->whereNull('guardian_web_id');
        })->get();

        $hp = PhoneNumber::first()->no_tlp;

        return response()->view('guest.pagenotfound', compact('category', 'hp'), 404);
    }

    public function whatsapp(Request $request)
    {
        $article = ArticleShow::find($request->id);

        $nowHour = Carbon::now()->format('Y-m-d H:00:00');

        // Cari traffic dalam jam yang sama
        $watraffic = WaTraffic::where('article_show_id', $article->id)
            ->whereBetween('created_at', [
                Carbon::parse($nowHour),
                Carbon::parse($nowHour)->addHour()
            ])
            ->first();

        if ($watraffic) {
            // Sudah ada record di jam ini → tambah access
            $watraffic->increment('access');
        } else {
            // Belum ada record → buat baru
            WaTraffic::create([
                'article_show_id' => $article->id,
                'access' => 1,
            ]);
        }

        $message = 'Halo saya dapat info dari '. $article->slug;

        $url = 'https://wa.me/' . $request->phone. '?text=' . urlencode($message);

        return redirect()->away($url);
    }

    public function test()
    {
        $duplikatJudul = ArticleShow::select('judul')
            ->groupBy('judul')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('judul');

        if ($duplikatJudul->isEmpty()) {
            return response()->json([
                'status' => 'ok',
                'message' => 'Tidak ada judul yang duplikat.',
                'data' => []
            ]);
        }

        return response()->json([
            'status' => 'duplikat',
            'message' => 'Ditemukan judul yang duplikat.',
            'data' => $duplikatJudul
        ]);
    }
}
