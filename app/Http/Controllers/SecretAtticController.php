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
        $userId = $request->user()->id; // Assuming the authenticated user object is available in the request

        try {
            $user = User::findOrFail($userId); // Fetch the user from the database
            $secretAtticBooks = $user->secretattic->toArray()['books']; // Convert the collection to an array and access the books field

            if (count($secretAtticBooks) === 0) {
                return response()->json(['message' => 'No books found in secretattic'], 404);
            }

            $randomIndex = array_rand($secretAtticBooks);
            $randomBook = $secretAtticBooks[$randomIndex];

            return response()->json(['url' => $randomBook['URL']]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error retrieving random book'], 500);
        }
    }
    public function index()
    {
        $user = Auth::user();

        $secretAttic = SecretAttic::all();

        return response()->json(['data' => $secretAttic]);
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

        // Assuming you have defined the relationship between SecretAttic and Book in your models

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


}
