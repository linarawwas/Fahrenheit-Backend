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

    // other controller methods...
}
