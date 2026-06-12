<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = [
        'title',
        'author',
        'genre',
        'price',
        'description',
        'image',
        'year',
        'pages',
        'isbn',
        'publisher',
        'language',
        'is_new',
        'is_popular'
    ];
    
}
