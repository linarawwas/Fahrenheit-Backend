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

}
