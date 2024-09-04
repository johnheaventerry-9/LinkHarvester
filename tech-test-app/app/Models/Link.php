<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Link extends Model
{
    protected $fillable = ['title', 'url', 'comments', 'tags', 'is_live'];

    protected $casts = [
        'tags' => 'array',  // Cast 'tags' to an array since it's stored as JSON in the database
        'is_live' => 'boolean',  // Cast 'is_live' to a boolean
    ];
}
