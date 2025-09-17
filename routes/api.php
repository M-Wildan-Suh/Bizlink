<?php

use App\Http\Controllers\ArticleApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/sitemap/{code}', [ArticleApiController::class, 'sitemap']);

Route::middleware('daily_schedule')->group(function () {
    Route::get('/article/{code}', [ArticleApiController::class, 'index']);
    Route::get('/article/user/{user}/{code}', [ArticleApiController::class, 'indexUser']);
    Route::get('/article/category/{category}/{code}', [ArticleApiController::class, 'indexCategory']);
    Route::get('/article/tag/{tag}/{code}', [ArticleApiController::class, 'indexTag']);
    Route::get('/article/slug/{slug}/{code}', [ArticleApiController::class, 'landingPage']);
    Route::get('/article/notfound/{code}', [ArticleApiController::class, 'notFound']);
});
