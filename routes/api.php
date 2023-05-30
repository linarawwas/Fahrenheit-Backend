<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\NotesController;
use App\Http\Controllers\ReadingProgressController;
use App\Http\Controllers\scraping;
use App\Http\Controllers\SecretAtticController;
use App\Http\Controllers\StreakController;
use App\Http\Controllers\UserController;
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
Route::get('/users', [UserController::class, 'viewUsers'])->middleware('auth:sanctum');
Route::get('/user/username', [UserController::class, 'getUsername'])->middleware('auth:sanctum');
Route::get('/user/profile-picture', [UserController::class, 'getProfilePicture'])->middleware('auth:sanctum');
Route::put('/user', [UserController::class, 'update'])->middleware('auth:sanctum');
Route::get('/user/readingrank', [UserController::class, 'getReadingRank'])->middleware('auth:sanctum');

// This route retrieves all users and requires authentication with Sanctum
Route::get('/profile', [UserController::class, 'getAuthenticatedUser'])->middleware('auth:sanctum');

// This route stores a new book in the database
Route::post('/books', [BookController::class, 'store'])->middleware('auth:sanctum');;

// This route retrieves all books from the database
Route::get('/books', [BookController::class, 'getall'])->middleware('auth:sanctum');
Route::get('books/random', [BookController::class, 'getRandom'])->middleware('auth:sanctum');
Route::put('/books/increment-rating', [BookController::class, 'incrementRating'])->middleware('auth:sanctum');

Route::post('/secret-attic/add-book', [SecretAtticController::class, 'addToSecretAttic'])->middleware('auth:sanctum');
Route::get('secret-attic/books', [SecretAtticController::class, 'viewBooks'])->middleware('auth:sanctum');
Route::get('/secret-attic/book-urls', [SecretAtticController::class, 'viewBookUrls'])->middleware('auth:sanctum');
// Route::get('/secret-attic', [SecretAtticController::class, 'index'])->middleware('auth:sanctum');

// This route scrapes a website to get books and store them in the database
Route::get('scrape-books', [scraping::class, 'scrapeBooks'])->middleware('auth:sanctum');
Route::get('/secret-attic/scrape-page-contents', [scraping::class, 'scrapePageContents'])->middleware('auth:sanctum');

// This route starts a new reading progress for a user and a book
Route::post('/readingprogress/start-reading', [ReadingProgressController::class, 'start_reading'])->middleware('auth:sanctum');

// This route finishes a reading progress for a user and a book
Route::put('/readingprogress/finish-reading', [ReadingProgressController::class, 'updateFinishedReading'])->middleware('auth:sanctum');;

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
