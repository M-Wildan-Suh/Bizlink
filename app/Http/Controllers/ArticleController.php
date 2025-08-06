<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleBanner;
use App\Models\ArticleCategory;
use App\Models\ArticleGallery;
use App\Models\ArticleShow;
use App\Models\ArticleShowGallery;
use App\Models\ArticleTag;
use App\Models\GuardianWeb;
use App\Models\SourceCode;
use App\Models\Template;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ArticleController extends Controller
{
    public function generatearticle($id, Request $request)
    {
        $article = Article::findOrFail($id);
        
        $hasScheduleStatus = $article->articleshow()->where('status', 'schedule')->exists();

        if ($hasScheduleStatus) {
            $article->schedule = true;
        } else {
            $article->schedule = $request->schedule;
        }

        $article->save();
        
        $total = (int) $request->total;

        $maxAttempts = 1000; // Lebih fleksibel
        $attempts = 0;
        $savedCount = 0;

        while ($savedCount < $total && $attempts < $maxAttempts) {
            $spinnedTitle = $this->spinText($article->judul);
            $spinnedBody = $this->spinText($article->article);

            // Gabungkan keduanya untuk ambil semua tag
            $combinedText = $spinnedTitle . ' ' . $spinnedBody;

            // Ambil semua [tag]
            preg_match_all('/\[[^\]]+\]/', $combinedText, $matches);

            // Ambil tag unik saja
            $tags = array_unique($matches[0] ?? []);

            foreach ($tags as $tag) {
                $source = SourceCode::where('title', $tag)->first();
                if ($source) {
                    // Pecah berdasarkan koma, dan ambil 1 secara random
                    $options = array_map('trim', explode(',', $source->content));
                    $replacement = $options[array_rand($options)];

                    // Ganti di kedua teks
                    $spinnedTitle = str_replace($tag, $replacement, $spinnedTitle);
                    $spinnedBody = str_replace($tag, $replacement, $spinnedBody);
                }
            }

            $spinnedBody = str_replace('[pa_judul]', $spinnedTitle, $spinnedBody);

            $isDuplicate = ArticleShow::where('judul', $spinnedTitle)
                ->orWhere('article', $spinnedBody)
                ->exists();

            if (!$isDuplicate) {
                $newArticleShow = new ArticleShow;

                $newArticleShow->article_id = $article->id;
                $newArticleShow->judul = $spinnedTitle;
                $newArticleShow->slug = Str::slug($newArticleShow->judul);
                $newArticleShow->article = $spinnedBody;
                $newArticleShow->template_id = optional($article->template->random())->id;
                $newArticleShow->banner = $article->articlebanner->isNotEmpty() ? $article->articlebanner->random()->image : null;

                if ($request->schedule == true) {
                    $newArticleShow->status = 'schedule';
                } else {
                    $newArticleShow->status = 'publish';
                }

                $newArticleShow->save();
                
                $galleries = $article->articlegallery->shuffle()->take(6);
                foreach ($galleries as $gallery) {
                    $showGallery = new ArticleShowGallery;
                    $showGallery->article_show_id = $newArticleShow->id;
                    $showGallery->article_gallery_id = $gallery->id;
                    $showGallery->image = $gallery->image;
                    $showGallery->image_alt = $gallery->image_alt;
                    $showGallery->save();
                }
                $savedCount++;
            }

            $attempts++;
        }

        return redirect()->back()->with('status', "$savedCount artikel berhasil dibuat.");
    }


    private function spinText($text)
    {
        while (preg_match('/\{([^{}]*)\}/', $text)) {
            $text = preg_replace_callback('/\{([^{}]*)\}/', function ($matches) {
                $options = explode('|', $matches[1]);
                return $options[array_rand($options)];
            }, $text);
        }
    
        return $text;
    }

    public function shuffle($id) 
    {
        $article = Article::find($id);
        $articleshow = $article->articleshow;

        foreach ($articleshow as $item) {
            // Hapus gallery lama
            ArticleShowGallery::where('article_show_id', $item->id)->delete();

            // Shuffle dan simpan banner
            $item->banner = $article->articlebanner->isNotEmpty()
                ? $article->articlebanner->random()->image
                : null;

            // Ambil gallery acak
            $galleries = $article->articlegallery->shuffle()->take(6);

            // Simpan gallery baru
            foreach ($galleries as $gallery) {
                $showGallery = new ArticleShowGallery;

                $showGallery->article_show_id = $item->id;
                $showGallery->article_gallery_id = $gallery->id;
                $showGallery->image = $gallery->image;
                $showGallery->image_alt = $gallery->image_alt;

                $showGallery->save();
            }

            $item->save();
        }

        return redirect()->back();
    }

    private function formatCount($number)
    {
        if ($number >= 1000) {
            return round($number / 1000, 1) . 'k'; // contoh: 1500 → 1.5k
        }
        return (string) $number;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, $status = null, $filtercat = null, $filterweb = null)
    {
        $category = ArticleCategory::all();

        $web = GuardianWeb::all();
        
        $filter = $status === 'schedule' ? 1 : 0;

        $data = Article::
            when($filterweb === 'main', function ($query) {
                $query->whereNull('guardian_web_id');
            })
            ->when($filterweb && $filterweb != 'main' && $filterweb != 'all', function ($query) use ($filterweb) {
                $query->where('guardian_web_id', $filterweb);
            })
            ->when($filtercat && $filtercat != 'all', function ($query) use ($filtercat){
                $query->whereHas('articlecategory', function ($q) use ($filtercat) {
                    $q->where('category_id', $filtercat);
                });
            })
            ->withCount(['articleshow as articleshow_private_count' => function ($query) {
                $query->where('status', 'private');
            }])
            ->when($status !== 'all' && $status, function ($query) use ($status, $filter) {
                $query->where('schedule', $filter);
                
                if ($status === 'private') {
                    $query->where('articleshow_private_count', '>', 0);
                } else {
                    $query->where('articleshow_private_count', '=', 0);
                }
            })
            ->latest()
            ->simplePaginate(20);
            
        if ($request->ajax()) {
            return view('admin.article.row', compact('data'))->render();
        }

        return view('admin.article.index' ,compact('data', 'category', 'web', 'status', 'filtercat', 'filterweb'));
    }

    public function indexspintax(Request $request, $status = null, $filtercat = null, $filterweb = null)
    {
        $category = ArticleCategory::all();

        $web = GuardianWeb::all();
        
        $filter = $status === 'schedule' ? 1 : 0;

        $data = Article::where('article_type', 'spintax')
            ->when($filterweb === 'main', function ($query) {
                $query->whereNull('guardian_web_id');
            })
            ->when($filterweb && $filterweb != 'main' && $filterweb != 'all', function ($query) use ($filterweb) {
                $query->where('guardian_web_id', $filterweb);
            })
            ->when($filtercat && $filtercat != 'all', function ($query) use ($filtercat){
                $query->whereHas('articlecategory', function ($q) use ($filtercat) {
                    $q->where('category_id', $filtercat);
                });
            })
            ->when($request->search, function ($query) use ($request) {
                $query->where('judul', 'like', '%' . $request->search . '%');
            })
            ->withCount(['articleshow as articleshow_private_count' => function ($query) {
                $query->where('status', 'private');
            }])
            ->when($status !== 'all' && $status, function ($query) use ($status, $filter) {
                $query->where('schedule', $filter);
                
                if ($status === 'private') {
                    $query->where('articleshow_private_count', '>', 0);
                } else {
                    $query->where('articleshow_private_count', '=', 0);
                }
            })
            ->latest()
            ->simplePaginate(20);
            
        if ($request->ajax()) {
            return view('admin.article.row', compact('data'))->render();
        }

        return view('admin.article.index' ,compact('data', 'category', 'web', 'status', 'filtercat', 'filterweb'));
    }
    
    public function indexunique(Request $request, $status = null, $filtercat = null, $filterweb = null)
    {
        $category = ArticleCategory::all();

        $web = GuardianWeb::all();
        
        $filter = $status === 'schedule' ? 1 : 0;

        $data = Article::where('article_type', 'unique')
            ->when($filterweb === 'main', function ($query) {
                $query->whereNull('guardian_web_id');
            })
            ->when($filterweb && $filterweb != 'main' && $filterweb != 'all', function ($query) use ($filterweb) {
                $query->where('guardian_web_id', $filterweb);
            })
            ->when($filtercat && $filtercat != 'all', function ($query) use ($filtercat){
                $query->whereHas('articlecategory', function ($q) use ($filtercat) {
                    $q->where('category_id', $filtercat);
                });
            })
            ->when($request->search, function ($query) use ($request) {
                $query->where('judul', 'like', '%' . $request->search . '%');
            })
            ->withCount(['articleshow as articleshow_private_count' => function ($query) {
                $query->where('status', 'private');
            }])
            ->when($status !== 'all' && $status, function ($query) use ($status, $filter) {
                $query->where('schedule', $filter);
                
                if ($status === 'private') {
                    $query->where('articleshow_private_count', '>', 0);
                } else {
                    $query->where('articleshow_private_count', '=', 0);
                }
            })
            ->latest()
            ->simplePaginate(20);
            
        if ($request->ajax()) {
            return view('admin.article.row', compact('data'))->render();
        }

        return view('admin.article.index' ,compact('data', 'category', 'web', 'status', 'filtercat', 'filterweb'));
    }

    public function spin($id, Request $request) 
    {
        $count = new \stdClass();
        $count->all = ArticleShow::where('article_id', $id)->count();
        $count->schedule = ArticleShow::where('article_id', $id)->where('status', 'schedule')->count();
        $count->publish = ArticleShow::where('article_id', $id)->where('status', 'publish')->count();
        $count->private = ArticleShow::where('article_id', $id)->where('status', 'private')->count();

        $article = Article::find($id);

        $data = ArticleShow::where('article_id', $id)
            ->when($request->search, function ($query) use ($request) {
                $query->where('judul', 'like', '%' . $request->search . '%');
            })
            ->paginate(10);

        return view('admin.article.index-spin', compact('article', 'data', 'count'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tag = ArticleTag::all();
        $template = Template::all();
        $category = ArticleCategory::all();
        $guardian = GuardianWeb::all();
        return view('admin.article.create-spintax', compact('tag', 'category', 'template', 'guardian'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'judul' => 'required|unique:'.Article::class.'|unique:'.ArticleShow::class,
                'category' => 'array',
                'tag' => 'array',
                'template_id' => 'required|array',
                'article' => 'required',
            ]);
    
            // Proses simpan data jika valid
        } catch (ValidationException $e) {
            // Hapus input lama otomatis dari Laravel
            Session::forget('_old_input');
        
            // Set ulang dengan input yang dimodifikasi
            $oldInput = $request->except(['_token', 'image']);
            // $oldInput['judul'] = ($request->input('judul') ?? '') . ' paksa';
            if ($request->has('category')) {
                $oldInput['category'] = collect($request->category)
                    ->map(fn($item) => (object) ['category' => $item])  // Mengubah setiap item menjadi objek
                    ->pipe(function($collection) {
                        return new \Illuminate\Database\Eloquent\Collection($collection->all());  // Mengubah menjadi Eloquent Collection
                    });
            }
            if ($request->has('tag')) {
                $oldInput['tag'] = collect($request->tag)
                    ->map(fn($item) => (object) ['tag' => $item])  // Mengubah setiap item menjadi objek
                    ->pipe(function($collection) {
                        return new \Illuminate\Database\Eloquent\Collection($collection->all());  // Mengubah menjadi Eloquent Collection
                    });
            }
        
            Session::flashInput($oldInput);
        
            return redirect()
                ->back()
                ->withErrors($e->validator);
        }

        // dd($request);
        $newarticle = new Article;

        $newarticle->user_id = Auth::id();
        $newarticle->judul = $request->judul;
        $newarticle->article = $request->article;
        $newarticle->guardian_web_id = $request->guardian;
        $newarticle->article_type = 'spintax';

        // Cek apakah link adalah YouTube
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $request->link, $matches)) {
            $videoId = $matches[1];
            $newarticle->video_type = "youtube";
            $newarticle->youtube = "https://www.youtube.com/embed/{$videoId}";
            $newarticle->tiktok = null;
        } elseif (preg_match('/(?:www\.)?tiktok\.com\/(@[\w.-]+)\/video\/(\d+)/', $request->link, $matches)) {
            $newarticle->video_type = "tiktok";
            $newarticle->youtube = null;
            $newarticle->tiktok = "https://www.tiktok.com/{$matches[0]}";
        } else {
            $newarticle->video_type = "none";
            $newarticle->youtube = null;
            $newarticle->tiktok = null;
        }

        $newarticle->save();

        $newarticle->template()->sync($request->template_id);
        
        $newbanner = new ArticleBanner;

        $newbanner->article_id = $newarticle->id;

        // Banner
        if ($request->has('image_banner') && !empty($request->image_banner)) {
            foreach ($request->image_banner as $image) {
                $newgallery = new ArticleBanner;
                $newgallery->article_id = $newarticle->id;
        
                // Pastikan image adalah instance dari UploadedFile
                if ($image instanceof \Illuminate\Http\UploadedFile && $image->isValid()) {
                    // Ambil nama file tanpa ekstensi
                    $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                    
                    // Tambahkan tanggal saat ini
                    $currentDate = now()->format('YmdHis');
                    
                    // Gabungkan nama file dan tanggal input
                    $imageName = $originalName . '_' . $currentDate;
        
                    $imagePath = public_path('storage/images/article/banner/');

                    if (!File::exists($imagePath)) {
                        File::makeDirectory($imagePath, 0755, true);
                    }
        
                    $manager = new ImageManager(new Driver());
                    $imageOptimized = $manager->read($image->getPathname());
                    $imageFullPath = $imagePath . $imageName . '.webp';
                    $imageOptimized->save($imageFullPath);
        
                    // Simpan nama file dengan ekstensi .webp
                    $newgallery->image = $imageName . '.webp';
                    $newgallery->image_alt = $imageName;
                }
        
                $newgallery->save();
            }
        }

        // Tag
        if ($request->tag) {
            $tags = array_map(fn($item) => ucfirst($item), $request->tag);
        
            $tagIds = [];
            foreach ($tags as $tagName) {
                $formattedTagName = Str::title($tagName);
                $slug = Str::slug($tagName);

                $tag = ArticleTag::firstOrCreate(
                    ['slug' => $slug],
                    ['tag' => $formattedTagName]
                );

                $tagIds[] = $tag->id;
            }

            $newarticle->articletag()->attach($tagIds);
        }

        // Category
        if ($request->category) {
            $category = array_map(fn($item) => ucfirst($item), $request->category);
        
            $categoryIds = [];
            foreach ($category as $categoryName) {
                $formattedCategoryName = Str::title($categoryName);
                $slug = Str::slug($categoryName);

                $category = ArticleCategory::firstOrCreate(
                    ['slug' => $slug],
                    ['category' => $formattedCategoryName]
                );

                $categoryIds[] = $category->id;
            }

            $newarticle->articlecategory()->attach($categoryIds);
        }

        // Gallery
        if ($request->has('image_gallery') && !empty($request->image_gallery)) {
            foreach ($request->image_gallery as $image) {
                $newgallery = new ArticleGallery;
                $newgallery->article_id = $newarticle->id;
        
                // Pastikan image adalah instance dari UploadedFile
                if ($image instanceof \Illuminate\Http\UploadedFile && $image->isValid()) {
                    // Ambil nama file tanpa ekstensi
                    $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
                    
                    // Tambahkan tanggal saat ini
                    $currentDate = now()->format('YmdHis');
                    
                    // Gabungkan nama file dan tanggal input
                    $imageName = $originalName . '_' . $currentDate;
        
                    $imagePath = public_path('storage/images/article/gallery/');

                    if (!File::exists($imagePath)) {
                        File::makeDirectory($imagePath, 0755, true);
                    }
        
                    $manager = new ImageManager(new Driver());
                    $imageOptimized = $manager->read($image->getPathname());
                    $imageFullPath = $imagePath . $imageName . '.webp';
                    $imageOptimized->save($imageFullPath);
        
                    // Simpan nama file dengan ekstensi .webp
                    $newgallery->image = $imageName . '.webp';
                    $newgallery->image_alt = $imageName;
                }
        
                $newgallery->save();
            }
        }

        return redirect()->route('article.index')->with('success', 'Artikel berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Article $article)
    {
        $tagid = $article->articletag->pluck('id')->toArray();
        $tag = ArticleTag::whereNotIn('id', $tagid)->get();
        $categoryid = $article->articlecategory->pluck('id')->toArray();
        $category = ArticleCategory::whereNotIn('id', $categoryid)->get();
        $template = Template::all();
        $guardian = GuardianWeb::all();
        return view('admin.article.edit-spintax', compact('article', 'tag', 'category', 'template', 'guardian'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Article $article)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Article $article)
    {
        try {
            $validated = $request->validate([
                'judul' => [
                    'required',
                    Rule::unique('articles')->ignore($article->id),
                    Rule::unique('article_shows'),
                ],
                'category' => 'array',
                'tag' => 'array',
                'template_id' => 'required|array',
                'article' => 'required',
            ]);
    
            // Proses simpan data jika valid
        } catch (ValidationException $e) {
            // Hapus input lama otomatis dari Laravel
            Session::forget('_old_input');
        
            // Set ulang dengan input yang dimodifikasi
            $oldInput = $request->except(['_token', 'image']);
            // $oldInput['judul'] = ($request->input('judul') ?? '') . ' paksa';
            if ($request->has('category')) {
                $oldInput['category'] = collect($request->category)
                    ->map(fn($item) => (object) ['category' => $item])  // Mengubah setiap item menjadi objek
                    ->pipe(function($collection) {
                        return new \Illuminate\Database\Eloquent\Collection($collection->all());  // Mengubah menjadi Eloquent Collection
                    });
            }
            if ($request->has('tag')) {
                $oldInput['tag'] = collect($request->tag)
                    ->map(fn($item) => (object) ['tag' => $item])  // Mengubah setiap item menjadi objek
                    ->pipe(function($collection) {
                        return new \Illuminate\Database\Eloquent\Collection($collection->all());  // Mengubah menjadi Eloquent Collection
                    });
            }
        
            Session::flashInput($oldInput);
        
            return redirect()
                ->back()
                ->withErrors($e->validator);
        }

        $article->judul = $request->judul;
        $article->article = $request->article;
        $article->guardian_web_id = $request->guardian;


        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $request->link, $matches)) {
            $videoId = $matches[1];
            $article->video_type = "youtube";
            $article->youtube = "https://www.youtube.com/embed/{$videoId}";
            $article->tiktok = null;
        } elseif (preg_match('/(?:www\.)?tiktok\.com\/(@[\w.-]+)\/video\/(\d+)/', $request->link, $matches)) {
            $article->video_type = "tiktok";
            $article->youtube = null;
            $article->tiktok = "https://www.tiktok.com/{$matches[0]}";
        } else {
            $article->video_type = "none";
            $article->youtube = null;
            $article->tiktok = null;
        }

        $article->save();

        $article->template()->sync($request->template_id);

        if ($request->tag) {
            // Ubah tag menjadi huruf besar di awal
            $tags = array_map(fn($item) => ucfirst($item), $request->tag);
        
            // Pastikan setiap tag ada di database
            $tagIds = [];
            foreach ($tags as $tagName) {
                $formattedTagName = Str::title($tagName);
                $slug = Str::slug($tagName);

                $tag = ArticleTag::firstOrCreate(
                    ['slug' => $slug],
                    ['tag' => $formattedTagName]
                );

                $tagIds[] = $tag->id;
            }
        
            // Sinkronkan tag ke dalam pivot table
            $article->articletag()->sync($tagIds);
        }

        // Category
        if ($request->category) {
            // Ubah tag menjadi huruf besar di awal
            $categories = array_map(fn($item) => ucfirst($item), $request->category);
        
            // Pastikan setiap tag ada di database
            $categoryIds = [];
            foreach ($categories as $categoryName) {
                $formattedCategoryName = Str::title($categoryName);
                $slug = Str::slug($categoryName);

                $category = ArticleCategory::firstOrCreate(
                    ['slug' => $slug],
                    ['category' => $formattedCategoryName]
                );

                $categoryIds[] = $category->id;
            }
        
            // Sinkronkan tag ke dalam pivot table
            $article->articlecategory()->sync($categoryIds);
        }

        return redirect()->back()->with('success', 'Artikel berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */

    public function generatearticledestroy($id, Request $request)
    {
        $ids = ArticleShow::where('article_id', $id)
            ->orderBy('created_at', 'asc')
            ->limit($request->total)
            ->pluck('id');

        ArticleShow::whereIn('id', $ids)->delete();

        return redirect()->back();
    }

    public function destroy(Article $article)
    {
        if ($article->articlebanner) {
            foreach ($article->articlebanner as $item) {
                $path = public_path('storage/images/article/banner/' . $item->image);
            
                if (file_exists($path)) {
                    unlink($path);
                }
            }
        }
        if ($article->articlegallery) {
            foreach ($article->articlegallery as $item) {
                $path = public_path('storage/images/article/gallery/' . $item->image);
            
                if (file_exists($path)) {
                    unlink($path);
                }
            }
        }
        
        $article->delete();

        return redirect()->back();
    }
}
