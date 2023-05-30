<?php

namespace App\Http\Controllers;

use App\Models\SecretBook;
use App\Models\SecretVault;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SecretBooksController extends Controller
{
    // Function to store a single secret book
    public function store(Request $request)
    {
        try {
            $user = Auth::user();

            $validatedData = $request->validate([
                'pg_id' => 'required',
                'title' => 'required',
                'author' => 'required',
                'language' => 'required',
                'url' => 'required',
                'ratings' => 'nullable',
                'price' => 'nullable',
            ]);

            $secretBook = SecretBook::create($validatedData);

            return response()->json(['message' => 'Secret book stored successfully', 'data' => $secretBook], 201);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 400);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to store secret book', 'error' => $e->getMessage()], 500);
        }
    }

    // Function to store multiple secret books

    public function storeMultiple(Request $request)
    {
        try {
            $user = Auth::user();

            $validatedData = $request->validate([
                'books.*.pg_id' => 'required',
                'books.*.title' => 'required',
                'books.*.author' => 'required',
                'books.*.language' => 'required',
                'books.*.url' => 'required',
                'books.*.ratings' => 'nullable',
                'books.*.price' => 'nullable',
            ]);

            $secretBooksData = [];

            foreach ($validatedData['books'] as $bookData) {
                $secretBook = [
                    'pg_id' => $bookData['pg_id'],
                    'title' => $bookData['title'],
                    'author' => $bookData['author'],
                    'language' => $bookData['language'],
                    'url' => $bookData['url'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                if (array_key_exists('ratings', $bookData)) {
                    $secretBook['ratings'] = $bookData['ratings'];
                }

                if (array_key_exists('price', $bookData)) {
                    $secretBook['price'] = $bookData['price'];
                }

                $secretBooksData[] = $secretBook;
            }

            $secretBooks = SecretBook::insert($secretBooksData);

            return response()->json(['message' => 'Multiple secret books stored successfully', 'data' => $secretBooks], 201);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 400);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to store multiple secret books', 'error' => $e->getMessage()], 500);
        }
    }

    public function updatePrice(Request $request)
    {
        $user = Auth::user();

        try {
            $validatedData = $request->validate([
                'book_id' => 'required',
                'price' => 'required',
            ]);

            $bookId = $validatedData['book_id'];

            $secretBook = SecretBook::findOrFail($bookId);

            $oldPrice = $secretBook->price;
            $newPrice = $validatedData['price'];

            // Update the price field
            $secretBook->price = $newPrice;
            $secretBook->save();

            return response()->json(['message' => 'Price updated successfully', 'old_price' => $oldPrice, 'new_price' => $newPrice], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Secret book not found'], 404);
        } catch (ValidationException $e) {
            return response()->json(['message' => 'Validation failed', 'errors' => $e->errors()], 400);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update price', 'error' => $e->getMessage()], 500);
        }
    }

    public function purchaseBook(Request $request)
    {
        $user = $request->user();
        $bookId = $request->input('book_id');

        try {
            $book = SecretBook::findOrFail($bookId);

            if ($user->reading_rank >= $book->price) {
                $user->reading_rank -= $book->price;
                $user->save();

                $secretVault = $user->secretVault;

                if (!$secretVault) {
                    $secretVault = SecretVault::create([
                        'user_id' => $user->id,
                    ]);
                }

                $secretVault->secretBooks()->attach($book);

                return response()->json(['message' => 'Book purchased successfully']);
            }

            return response()->json(['message' => 'Insufficient reading rank to purchase the book'], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to purchase book', 'error' => $e->getMessage()], 500);
        }
    }


    public function getAllBooks()
    {
        $user = Auth::user();

        $books = SecretBook::all();

        return response()->json($books);
    }
    public function getUserSecretVaultBooks()
    {
        $user = Auth::user();
    
        $secretVault = $user->secretVault;
    
        if (!$secretVault) {
            return response()->json(['message' => 'User does not have a secret vault'], 404);
        }
    
        $books = $secretVault->secretBooks()->get();
    
        return response()->json(['books' => $books], 200);
    }
    
// ...

public function deleteAllSecretBooks()
{
    SecretBook::query()->delete();

    return response()->json(['message' => 'All secret books deleted successfully'], 200);
}

}
