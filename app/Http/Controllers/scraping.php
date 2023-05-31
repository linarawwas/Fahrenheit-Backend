<?php

namespace App\Http\Controllers;

// use GuzzleHttp\Client;
// use Illuminate\Http\Request;
use App\Models\Book;
use Goutte\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\DomCrawler\Crawler;

class scraping extends Controller
{
    // public function scrapeBooks(Request $request)
    // {
    // $user = Auth::user();

    //     $startId = 67;
    //     $endId = 100;

    //     $books = [];

    //     for ($id = $startId; $id <= $endId; $id++) {
    //         $url = "https://www.gutenberg.org/cache/epub/{$id}/pg{$id}-images.html";

    //         // Initialize the Guzzle client
    //         $client = new Client();

    //         try {
    //             // Send a GET request to the URL
    //             $response = $client->request('GET', $url);

    //             // Get the contents of the response
    //             $contents = $response->getBody()->getContents();

    //             // Create a new Crawler object and pass in the contents
    //             $crawler = new Crawler($contents);

    //             // Find the p element containing the book title and extract the text following "Title: "
    //             $title = $crawler->filter('p:contains("Title: ")')->first()->text();
    //             $title = substr($title, strpos($title, 'Title: ') + 7);

    //             // Find the p element containing the book author and extract the text following "Author: "
    //             $author = $crawler->filter('p:contains("Author: ")')->first()->text();
    //             $author = substr($author, strpos($author, 'Author: ') + 8);

    //             // Find the p element containing the book language and extract the text following "Language: "
    //             $language = $crawler->filter('p:contains("Language: ")')->first()->text();
    //             $language = substr($language, strpos($language, 'Language: ') + 10);

    //             // Get the URL of the book
    //             $bookUrl = "https://www.gutenberg.org/cache/epub/{$id}/pg{$id}-images.html";
    //             // Add the book information to the array
    //             $books[] = [
    //                 'pg_id' => $id,
    //                 'title' => $title,
    //                 'author' => $author,
    //                 'language' => $language,
    //                 'url' => $bookUrl,
    //             ];
    //         } catch (\Exception $e) {
    //             // If the page is not found, continue to the next ID
    //             continue;
    //         }
    //     }

    //     // Return the scraped books as JSON
    //     return response()->json($books);
    // }

    public function BrowseBook(Request $request)
    {
        $user = Auth::user();

        $bookId = $request->input('book_id');

        // Retrieve the book URL based on the provided book ID
        $book = DB::table('books')->where('id', $bookId)->first();
        if (!$book) {
            return response()->json(['error' => 'Book not found'], 404);
        }
        $bookUrl = $book->url;

        // Create a new Goutte client
        $client = new Client();

        // Send a GET request to the book URL and retrieve the page contents
        $crawler = $client->request('GET', $bookUrl);

        // Find the starting node based on the specified path
        $startingNode = $crawler->filter('html > body > .pg-boilerplate.pgheader')->first();

        if (!$startingNode->count()) {
            return response()->json(['page_contents' => 'Starting node not found'], 404);
        }

        // Get the following siblings of the starting node within the same parent node
        $filteredNodes = $startingNode->siblings();

        // Filter the nodes to include only those after the starting node
        $filteredNodes = $filteredNodes->nextAll();

        // Extract the HTML contents of the filtered nodes
        $pageContents = $filteredNodes->each(function ($node) {
            return $node->getNode(0)->C14N();
        });

        // Concatenate the extracted HTML contents
        $responseContents = implode("\n", $pageContents);

        // Return the page contents as the response
        return response()->json(['page_contents' => $responseContents], 200);
    }

    public function scrapePageContents()
    {
        $user = Auth::user();

        // Assuming you have defined the relationship between SecretAttic and Book in your models

        // Retrieve the books associated with the user's secret attic
        $books = $user->secretAttic->books;

        // Get a random book
        $randomBook = $books->random();

        // Get the URL of the random book
        $randomURL = $randomBook->url;

        // Create a new Goutte client
        $client = new Client();

        // Send a GET request to the random URL and retrieve the page contents
        $crawler = $client->request('GET', $randomURL);

        // Find the starting node based on the specified path
        $startingNode = $crawler->filter('html > body > .pg-boilerplate.pgheader')->first();

        if (!$startingNode->count()) {
            return response()->json(['page_contents' => 'Starting node not found'], 404);
        }

        // Get the following siblings of the starting node within the same parent node
        $filteredNodes = $startingNode->siblings();

        // Filter the nodes to include only those after the starting node
        $filteredNodes = $filteredNodes->nextAll();

        // Extract the HTML contents of the filtered nodes
        $pageContents = $filteredNodes->each(function ($node) {
            return $node->getNode(0)->C14N();
        });

        // Concatenate the extracted HTML contents
        $responseContents = implode("\n", $pageContents);

        // Return the page contents as the response
        return response()->json(['page_contents' => $responseContents], 200);
    }
}
