<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\SecretAttic;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class SecretAtticController extends Controller
{
    public function getRandomBookURL(Request $request)
    {
        $userId = $request->user()->id;  // Assuming the authenticated user object is available in the request

        try {
            $user = User::findOrFail($userId);  // Fetch the user from the database
            $secretAtticBooks = $user->secretattic()
                ->where('received', false)  // Filter books where 'received' column is false
                ->get();  // Retrieve the filtered books

            if ($secretAtticBooks->isEmpty()) {
                return response()->json(['message' => 'No books found in secretattic'], 404);
            }

            $randomBook = $secretAtticBooks->random();  // Select a random book from the filtered books

            return response()->json(['url' => $randomBook->URL]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error retrieving random book'], 500);
        }
    }

    public function addToSecretAttic(Request $request)
    {
        // Get the authenticated user
        $user = Auth::user();

        // Check if the user has a secret attic
        if ($user->secretAttic === null) {
            // If the user does not have a secret attic, create one
            $secretAttic = SecretAttic::create(['user_id' => $user->id]);
        } else {
            // If the user already has a secret attic, use it
            $secretAttic = $user->secretAttic;
        }

        // Get the book ID from the request
        $bookId = $request->input('book_id');

        // Find the book by its ID
        $book = Book::findOrFail($bookId);

        // Attach the book to the secret attic
        $secretAttic->books()->attach($book);

        // Return a response indicating success
        return response()->json(['message' => 'Book added to secret attic successfully']);
    }

    public function viewBooks()
    {
        $user = Auth::user();
        // Retrieve the books associated with the user's secret attic
        $books = $user->secretAttic->books;

        // You can now return the books to your view or manipulate them as needed
        return response()->json(['books' => $books], 200);
    }

    public function viewBookUrls()
    {
        $user = Auth::user();

        // Retrieve the books associated with the user's secret attic
        $books = $user->secretAttic->books;

        // Extract the 'url' field from each book record
        $urls = $books->pluck('url');

        // Return the 'url' fields as a JSON response
        return response()->json(['urls' => $urls], 200);
    }

    public function countBooks()
    {
        $user = Auth::user();

        // Retrieve the books associated with the user's secret attic
        $books = $user->secretAttic->books;

        // Count the number of books
        $count = $books->count();

        // Return the count as a JSON response
        return response()->json(['count' => $count], 200);
    }

    public function markBookReceived(Request $request)
    {
        // Validate the request data
        $request->validate([
            'book_id' => 'required|exists:books,id',
        ]);

        try {
            // Get the authenticated user's secret attic
            $secretAttic = $request->user()->secretAttic;

            // Check if the user has a secret attic
            if (!$secretAttic) {
                return response()->json(['message' => 'User does not have a secret attic'], 404);
            }

            // Mark the book as received in the pivot table
            $secretAttic->books()->updateExistingPivot($request->input('book_id'), ['received' => true]);

            return response()->json(['message' => 'Book marked as received'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to mark the book as received', 'error' => $e->getMessage()], 500);
        }
    }
}
