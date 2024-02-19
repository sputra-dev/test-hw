<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class Category extends Model
{
    use HasApiTokens, Notifiable, HasFactory, SoftDeletes;

    protected $table = 'categories';

    protected $fillable = ['name', 'note'];

    public function book()
    {
        return $this->hasMany(Book::class);
    }
}
