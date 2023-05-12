<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\scraping;
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
 
Route::post('/tokens/create', function (Request $request) {
    $token = $request->user()->createToken($request->token_name);
 
    return ['token' => $token->plainTextToken];
});

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
});
Route::controller(BookController::class)->group(function () {
    Route::get('getall', '/books/getall');
    // Route::post('finished', '/books/finished');
    // Route::post('started', '/books/started');
    // Route::get('readinghistory', '/books/finished');
    // Route::get('currentlyreading', '/books/started');
});
Route::get('scrape-books', 'App\Http\Controllers\scraping@scrapeBooks');
