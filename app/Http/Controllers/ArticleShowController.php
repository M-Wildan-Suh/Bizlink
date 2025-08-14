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
use App\Models\PhoneNumber;
use App\Models\Template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class ArticleShowController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->search) {
            $data = Article::where('article_type', 'unique')->where('judul', 'like', '%' . $request->search . '%')->paginate(10);

        } else {
            $data = Article::where('article_type', 'unique')->with('articleshow')->paginate(10);
        }
        return view('admin.article.index' ,compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $tag = ArticleTag::all();
        $category = ArticleCategory::all();
        $template = Template::all();
        $first = PhoneNumber::orderBy('id')->first();
        $phonenumber = PhoneNumber::where('id', '!=', $first->id)->get();
        $guardian = GuardianWeb::all();
        return view('admin.article.create-unique', compact('template', 'tag', 'phonenumber', 'category', 'guardian'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request);
        try {
            $validated = $request->validate([
                'judul' => 'required|max:255|unique:' . ArticleShow::class,
                'category' => 'array',
                'tag' => 'array',
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

        DB::transaction(function () use ($request) {
            // Article
            $newarticle = new Article;
    
            $newarticle->user_id = Auth::id();
            $newarticle->judul = $request->judul;
            $newarticle->article = $request->article;
            $newarticle->guardian_web_id = $request->guardian;
    
            if ($request->status === "schedule") {
                $newarticle->schedule = true;
            } else {
                $newarticle->schedule = false;
            }
    
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
    
            $newarticle->template()->sync([$request->template_id]);
            
            $newbanner = null;
    
            // Banner
            if ($request->hasFile('image')) {
                $newbanner = new ArticleBanner;
        
                $newbanner->article_id = $newarticle->id;
    
                $imageFile = $request->file('image');
                $imageName = time();
                $imagePath = public_path('storage/images/article/banner/');
    
                // Pastikan direktori ada, jika tidak maka buat
                if (!File::exists($imagePath)) {
                    File::makeDirectory($imagePath, 0755, true);
                }
    
                $manager = new ImageManager(new Driver());
                $image = $manager->read($imageFile->getPathname());
    
                $imageFullPath = $imagePath . $imageName . '.webp';
                $image->save($imageFullPath);
    
                $newbanner->image = $imageName . '.webp';
                $newbanner->image_alt = $imageName;
                
                $newbanner->save();
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
    
            // Article Show
            $newarticleshow = new ArticleShow;
    
            if ($request->no_tlp) {
                $no_tlp = $request->no_tlp;
        
                if (substr($no_tlp, 0, 1) === '0') {
                    $no_tlp = '+62' . substr($no_tlp, 1);
                }
        
                $phoneNumber = PhoneNumber::firstOrCreate(
                    [
                        'no_tlp' => $no_tlp,
                        'type' => 'article'    
                    ]
                );
                
                $newarticleshow->phone_number_id = $phoneNumber->id;
            }
    
            $newarticleshow->article_id = $newarticle->id;
            $newarticleshow->banner = $newbanner->image ?? null;
            $newarticleshow->judul = $newarticle->judul;
            $newarticleshow->slug = Str::slug($newarticleshow->judul);
            $newarticleshow->article = $newarticle->article;
            $newarticleshow->template_id = $request->template_id;
            $newarticleshow->status = $request->status;
            $newarticleshow->telephone = $request->tlp;
            $newarticleshow->whatsapp = $request->wa;
    
            if ($request->status === 'schedule') {
                $newarticleshow->created_at = $request->release;
            }
    
            $newarticleshow->save();
    
            // Article Gallery Show
            $newarticlegallery = ArticleGallery::where('article_id', $newarticle->id)->get();
    
            foreach ($newarticlegallery as $item) {
                $newgalleryshow = new ArticleShowGallery;
                
                $newgalleryshow->article_show_id = $newarticleshow->id;
                $newgalleryshow->article_gallery_id = $item->id;
                $newgalleryshow->image = $item->image;
                $newgalleryshow->image_alt = $item->image_alt;
    
                $newgalleryshow->save();
            }
    
        });
        return redirect()->route('article.index')->with('success', 'Artikel berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(ArticleShow $articleShow)
    {
        $tagid = $articleShow->articles->articletag->pluck('id')->toArray();
        $tag = ArticleTag::whereNotIn('id', $tagid)->get();
        $categoryid = $articleShow->articles->articlecategory->pluck('id')->toArray();
        $category = ArticleCategory::whereNotIn('id', $categoryid)->get();
        $template = Template::all();
        $first = PhoneNumber::orderBy('id')->first();
        $phonenumber = PhoneNumber::where('id', '!=', $first->id)->where('id', '!=', $articleShow->phone_number_id)->get();
        $guardian = GuardianWeb::all();
        return view('admin.article.edit-unique', compact('articleShow', 'tag', 'template', 'phonenumber', 'category', 'guardian'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ArticleShow $articleShow)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ArticleShow $articleShow)
    {
        try {
            $validated = $request->validate([
                'judul' => [
                    'required',
                    'max:255',
                    Rule::unique('article_shows')->ignore($articleShow->id),
                ],
                'category' => 'array',
                'tag' => 'array',
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
        $newarticle = Article::find($articleShow->article_id);

        $newarticle->judul = $request->judul;
        $newarticle->article = $request->article;
        $newarticle->guardian_web_id = $request->guardian;

        if ($request->status === "schedule") {
            $newarticle->schedule = true;
        } else {
            $newarticle->schedule = false;
        }

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

        $newarticle->template()->sync([$request->template_id]);
        
        $banner = null;

        if ($request->hasFile('image')) {
            $banner = ArticleBanner::where('article_id', $articleShow->article_id)->first();

            $path = public_path('storage/images/article/banner/' . $banner->image);

            if (file_exists($path)) {
                unlink($path);
            }

            $imageFile = $request->file('image');
            $imageName = time();
            $imagePath = public_path('storage/images/article/banner/');

            // Pastikan direktori ada, jika tidak maka buat
            if (!File::exists($imagePath)) {
                File::makeDirectory($imagePath, 0755, true);
            }

            $manager = new ImageManager(new Driver());
            $image = $manager->read($imageFile->getPathname());

            $imageFullPath = $imagePath . $imageName . '.webp';
            $image->save($imageFullPath);

            $banner->image = $imageName . '.webp';
            $banner->image_alt = $imageName;

            $banner->save();
        }

        // Tag
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
            $newarticle->articletag()->sync($tagIds);
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
            $newarticle->articlecategory()->sync($categoryIds);
        }

        if ($request->no_tlp) {
            $no_tlp = $request->no_tlp;
    
            if (substr($no_tlp, 0, 1) === '0') {
                $no_tlp = '+62' . substr($no_tlp, 1);
            }
    
            $phoneNumber = PhoneNumber::firstOrCreate(
                [
                    'no_tlp' => $no_tlp,
                    'type' => 'article'    
                ]
            );
            
            $articleShow->phone_number_id = $phoneNumber->id;
        } else {
            $articleShow->phone_number_id = null;
        }

        if ($banner) {
            $articleShow->banner = $banner->image;
        }
        $articleShow->judul = $newarticle->judul;
        $articleShow->slug = Str::slug($articleShow->judul);
        $articleShow->article = $newarticle->article;
        $articleShow->template_id = $request->template_id;
        $articleShow->telephone = $request->tlp;
        $articleShow->whatsapp = $request->wa;
        
        if ($request->status === "schedule") {
            $articleShow->created_at = $request->release;
        } elseif ($articleShow->status === "schedule") {
            $articleShow->created_at = now();
        }

        $articleShow->status = $request->status;

        $articleShow->save();

        return redirect()->back()->with('success', 'Artikel berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ArticleShow $articleShow)
    {
        $articleShow->delete();

        return redirect()->back();
    }
}
