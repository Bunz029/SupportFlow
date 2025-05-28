<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeBase extends Model
{
    use HasFactory;

    protected $table = 'knowledge_base';

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'category_id',
        'views_count',
    ];

    protected $casts = [
        'views_count' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
} 