<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\AuthorController;

Route::get('/authors/{authorId}/books', [AuthorController::class, 'association']);

Route::apiResources([
    '/authors' => AuthorController::class,
    '/books' => BookController::class,
]);
