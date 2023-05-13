<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\ReadingStreak;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReadingProgressController extends Controller
{
    public function getReadingProgress(Request $request)
    {
        // $user = Auth::user();
        $user = $request->user();

        $readingProgress = $user->readingProgress()->get();

        return response()->json([
            'reading_progress' => $readingProgress
        ]);
    }

    public function readToday(Request $request)
    {
        // $user = Auth::user();
        $user = $request->user();

        $streak = $user->readingStreaks()->latest()->first();

        // If there is no existing streak, create a new one
        if (!$streak) {
            $streak = new ReadingStreak([
                'last_reading_day' => Carbon::now()->toDateString(),
                'streak' => 1
            ]);
            $user->readingStreaks()->save($streak);
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
            ], 400);
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

    public function start_reading(Request $request, Book $book)
    {
        $user = $request->user();
        $started_at = now();

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

    public function finish_reading(Request $request, Book $book)
    {
        $user = $request->user();
        $finished_at = now();

        $reading_progress = $user->readingProgress()->where('book_id', $book->id)->first();

        if (!$reading_progress) {
            return response()->json([
                'message' => 'Reading progress not found!'
            ], 404);
        }

        // Calculate reading duration
        $reading_duration = Carbon::parse($reading_progress->started_reading_at)
            ->diffInDays($finished_at);

        // Update reading progress with reading duration
        $reading_progress->update([
            'finished_reading_at' => $finished_at,
            'reading_duration' => $reading_duration
        ]);

        $user->increaseReadingRankOnFinish($book);

        return response()->json([
            'message' => 'Finished reading book',
            'book' => $book->title,
            'finished_reading_at' => $finished_at,
            'reading_duration' => $reading_duration . ' days'
        ]);
    }
}
