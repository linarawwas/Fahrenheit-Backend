<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\ReadingProgressController;
use App\Http\Controllers\StreakController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\NotesController;
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
// This route returns the authenticated user's information
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// This route creates a new token for the authenticated user
Route::post('/tokens/create', function (Request $request) {
    $token = $request->user()->createToken($request->token_name);
    return ['token' => $token->plainTextToken];
});

// This route group includes all the routes related to the authentication process
Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
});

// This route retrieves all users and requires authentication with Sanctum
Route::get('/users', [UserController::class, 'viewUsers'])->middleware('auth:sanctum')->middleware('auth:sanctum');

// This route stores a new book in the database
Route::post('/books', 'App\Http\Controllers\BookController@store');

// This route retrieves all books from the database
Route::get('/books', 'App\Http\Controllers\BookController@getall');

// This route scrapes a website to get books and store them in the database
Route::get('scrape-books', 'App\Http\Controllers\scraping@scrapeBooks');

// This route starts a new reading progress for a user and a book
Route::post('/books/{book}/start-reading', [ReadingProgressController::class, 'start_reading'])->middleware('auth:sanctum');

// This route finishes a reading progress for a user and a book
Route::post('/books/{book}/finish-reading', [ReadingProgressController::class, 'finish_reading'])->middleware('auth:sanctum');

// This route retrieves the reading progress for a user and a book
Route::get('/reading-progress', [ReadingProgressController::class, 'getReadingProgress'])->middleware('auth:sanctum');

// This route updates the last_reading_day for the authenticated user
Route::post('/read-today', [ReadingProgressController::class, 'readToday'])->middleware('auth:sanctum');

// This route retrieves the current reading streak for the authenticated user
Route::get('/reading-streak', [StreakController::class, 'getStreak'])->middleware('auth:sanctum');


// Add a new note
Route::post('/notes', [NotesController::class, 'addNote'])->middleware('auth:sanctum');

// Update an existing note
Route::put('/notes/{note}', [NotesController::class, 'updateNote'])->middleware('auth:sanctum');

// View all notes for the authenticated user
Route::get('/notes', [NotesController::class, 'viewAllNotes'])->middleware('auth:sanctum');

// View notes by book id
Route::get('/notes/book/{book}', [NotesController::class, 'viewNotesByBook'])->middleware('auth:sanctum');

//  delete note by note id
Route::delete('/notes/{id}', [NotesController::class, 'deleteNote'])
    ->middleware('auth:sanctum');
