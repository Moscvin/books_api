<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $table = 'books';

    use HasFactory;

    protected $guarded=false;
    
    protected $fillable = [
        'title',
        'author',
        'published_year',
        'genre',
    ];
}
