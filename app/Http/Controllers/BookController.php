<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class BookController extends Controller
{
    public function getall()
    {
        $books = Book::all();

        return response()->json($books);
    }

    public function getRandom(Request $request)
    {
        $user = $request->user();

        $book = Book::inRandomOrder()->first();

        return response()->json($book);
    }

    public function store(Request $request)
    {
        // Get the book data from the request
        $bookData = $request->input('book');

        // Loop through each book and save it to the database
        foreach ($bookData as $data) {
            // Create a new book model instance
            $book = new Book;

            // Set the book's attributes
            $book->pg_id = $data['pg_id'];
            $book->title = $data['title'];
            $book->author = $data['author'];
            $book->language = $data['language'];
            $book->url = $data['url'];

            // Save the book to the database
            $book->save();
        }

        // Return a success response
        return response()->json(['message' => 'Books saved successfully']);
    }

    public function incrementRating(Request $request)
    {
        $bookId = $request->input('book_id');

        $book = Book::findOrFail($bookId);
        $book->increment('ratings');

        return response()->json([
            'message' => 'Book rating incremented successfully.',
            'book' => $book,
        ]);
    }
}
