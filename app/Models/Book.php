<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Book extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'books';

    protected $fillable = [
        'category_id',
        'title',
        'cover',
        'author',
        'publisher',
        'publish_year',
        'status',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
