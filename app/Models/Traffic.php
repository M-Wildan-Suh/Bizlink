<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Traffic extends Model
{
    use HasFactory;
    protected $fillable = ['article_show_id', 'guardian_web_id' , 'article_id', 'access'];

    public function articleshow() {
        return $this->belongsTo(ArticleShow::class);
    }

    public function article() {
        return $this->belongsTo(Article::class);
    }

    public function guardianweb() {
        return $this->belongsTo(GuardianWeb::class);
    }
}
