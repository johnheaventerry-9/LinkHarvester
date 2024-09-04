<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Link;


Route::get('/', function () {
    return view('welcome');
});

Route::get('/links', function (Request $request) {
    $tags = $request->query('tags');

    if (empty($tags)) {
        return response()->json(Link::all());
    }

    $tagsArray = explode(',', $tags);

    $links = Link::where(function($query) use ($tagsArray) {
        foreach ($tagsArray as $tag) {
            $query->whereJsonContains('tags', $tag);
        }
    })->get();

    return response()->json($links);
});