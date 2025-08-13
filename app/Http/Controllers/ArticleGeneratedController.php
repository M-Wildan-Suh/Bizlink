<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleGallery;
use App\Models\ArticleShow;
use App\Models\ArticleShowGallery;
use App\Models\ArticleTag;
use App\Models\PhoneNumber;
use App\Models\Template;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ArticleGeneratedController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->search) {
            $data = Article::where('article_type', 'spintax')->where('judul', 'like', '%' . $request->search . '%')->latest()->paginate(10);

        } else {
            $data = Article::where('article_type', 'spintax')->with('articleshow')->latest()->paginate(10);
        }
        return view('admin.article.index' ,compact('data'));
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
    public function show(string $id)
    {
        $articleShow = ArticleShow::find($id);
        $tagid = $articleShow->articles->articletag->pluck('id')->toArray();
        $tag = ArticleTag::whereNotIn('id', $tagid)->get();
        $article = Article::find($articleShow->article_id);
        $template = Template::all();
        $first = PhoneNumber::orderBy('id')->first();
        $phonenumber = PhoneNumber::where('id', '!=', $first->id)->where('id', '!=', $articleShow->phone_number_id)->get();
        return view('admin.article.edit-generated', compact('article', 'articleShow', 'tag', 'template', 'phonenumber'));
    }

    /**
     * 
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'judul' => [
                'required',
                'max:255',
                Rule::unique('article_shows')->ignore($id),
            ],
            'article' => 'required',
        ]);

        // dd($request);

        $articleShow = ArticleShow::find($id);
        if ($request->banner) {
            $articleShow->banner = $request->banner;
        }

        if ($request->no_tlp) {
            $no_tlp = $request->no_tlp;
    
            if (substr($no_tlp, 0, 1) === '0') {
                $no_tlp = '+62' . substr($no_tlp, 1);
            }
    
            $phoneNumber = PhoneNumber::firstOrCreate(
                [
                    'no_tlp' => $no_tlp, 
                    'type' => 'article',
                ]
            );
            
            $articleShow->phone_number_id = $phoneNumber->id;
        } else {
            $articleShow->phone_number_id = null;
        }
        
        $articleShow->judul = $request->judul;
        $articleShow->slug = Str::slug($articleShow->judul);
        $articleShow->article = $request->article;
        $articleShow->template_id = $request->template_id;
        $articleShow->telephone = $request->tlp;
        $articleShow->whatsapp = $request->wa;

        foreach ($articleShow->articleshowgallery as $gallery) {
            $gallery->delete();
        }
        
        if ($request->gellery) {
            foreach ($request->gallery as $item) {
                $newgalleryshow = new ArticleShowGallery;
    
                $articlegallery = ArticleGallery::find($item);
                
                $newgalleryshow->article_show_id = $articleShow->id;
                $newgalleryshow->article_gallery_id = $item;
                $newgalleryshow->image = $articlegallery->image;
                $newgalleryshow->image_alt = $articlegallery->image_alt;
    
                $newgalleryshow->save();
            }
        }

        $articleShow->save();

        return redirect()->back()->with('success', 'Artikel berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
