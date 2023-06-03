<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\ReadingProgress;
use App\Models\ReadingStreak;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReadingProgressController extends Controller
{
    public function getReadingProgress(Request $request)
    {
        $user = $request->user();

        $readingProgress = $user->readingProgress()->get();

        return response()->json([
            'reading_progress' => $readingProgress
        ]);
    }

    public function readToday(Request $request)
    {
        $user = $request->user();

        $streak = $user->readingStreak()->latest()->first();

        // If there is no existing streak, create a new one
        if (!$streak) {
            $streak = new ReadingStreak([
                'last_reading_day' => Carbon::now()->toDateString(),
                'streak' => 1,
                'longest_streak' => 1
            ]);
            $user->readingStreak()->save($streak);
            return response()->json([
                'message' => 'New streak created!',
                'streak' => $streak->streak
            ]);
        }

        // Check if the user has already read today
        $today = Carbon::now()->toDateString();
        if ($streak->last_reading_day === $today) {
            return response()->json([
                'message' => 'You have already read today!'
            ]);
        }

        // Check if the last reading day was yesterday
        $yesterday = Carbon::yesterday()->toDateString();
        if ($streak->last_reading_day === $yesterday) {
            $streak->increment('streak');
        } else {
            $streak->streak = 1;
        }

        // Update the last_reading_day field
        $streak->last_reading_day = $today;
        $streak->save();

        // Update the streak field in the user table
        $user->update([
            'current_reading_streak' => $streak->streak,
            'longest_reading_streak' => max($streak->streak, $user->longest_reading_streak)
        ]);

        return response()->json([
            'message' => 'Last reading day updated!',
            'streak' => $streak->streak
        ]);
    }

    public function start_reading(Request $request)
    {
        $user = $request->user();
        $started_at = now();
        $bookId = $request->input('book_id');
        $book = Book::find($bookId);
        $user->books()->syncWithoutDetaching([
            $book->id => ['started_reading_at' => $started_at]
        ]);

        $user->increaseReadingRankOnStart($book);

        return response()->json([
            'message' => 'Started reading book',
            'book' => $book->title,
            'started_reading_at' => $started_at
        ]);
    }

    public function updateFinishedReading(Request $request)
    {
        $user = $request->user();
        $bookId = $request->input('book_id');
        $book = Book::find($bookId);

        if (!$book) {
            return response()->json(['error' => 'Book not found.'], 404);
        }

        // Update finished_reading_at timestamp
        $finishedAt = Carbon::now()->toDateString();  // Get the date instance in YYYY-MM-DD format
        $user->books()->syncWithoutDetaching([
            $book->id => ['finished_reading_at' => $finishedAt]
        ]);

        // Increase reading rank
        $user->increaseReadingRankOnFinish($book);

        return response()->json([
            'message' => 'Finished reading book',
            'book' => $book->title,
            'finished_reading_at' => $finishedAt
        ]);
    }
}
