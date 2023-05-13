<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Symfony\Component\DomCrawler\Crawler;

class scraping extends Controller
{
    public function scrapeBooks(Request $request)
    {
        $startId = 1;
        $endId = 6;

        $books = [];

        for ($id = $startId; $id <= $endId; $id++) {
            $url = "https://www.gutenberg.org/cache/epub/{$id}/pg{$id}-images.html";

            // Initialize the Guzzle client
            $client = new Client();

            try {
                // Send a GET request to the URL
                $response = $client->request('GET', $url);

                // Get the contents of the response
                $contents = $response->getBody()->getContents();

                // Create a new Crawler object and pass in the contents
                $crawler = new Crawler($contents);

                // Find the p element containing the book title and extract the text following "Title: "
                $title = $crawler->filter('p:contains("Title: ")')->first()->text();
                $title = substr($title, strpos($title, 'Title: ') + 7);

                // Find the p element containing the book author and extract the text following "Author: "
                $author = $crawler->filter('p:contains("Author: ")')->first()->text();
                $author = substr($author, strpos($author, 'Author: ') + 8);

                // Find the p element containing the book language and extract the text following "Language: "
                $language = $crawler->filter('p:contains("Language: ")')->first()->text();
                $language = substr($language, strpos($language, 'Language: ') + 10);

                // Get the URL of the book
                $bookUrl = "https://www.gutenberg.org/cache/epub/{$id}/pg{$id}-images.html";
                // Add the book information to the array
                $books[] = [
                    'pg_id' => $id,
                    'title' => $title,
                    'author' => $author,
                    'language' => $language,
                    'url' => $bookUrl,
                ];
            } catch (\Exception $e) {
                // If the page is not found, continue to the next ID
                continue;
            }
        }

        // Return the scraped books as JSON
        return response()->json($books);
    }
}
