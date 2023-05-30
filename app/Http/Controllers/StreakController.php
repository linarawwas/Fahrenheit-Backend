<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StreakController extends Controller
{
    public function getStreak(Request $request)
    {
        $user = $request->user();

        // Retrieve the streak
        $streak = $user->readingStreak()->first();

        // If there is no existing streak, create a new one
        if (!$streak) {
            $streak = new ReadingStreak([
                'last_reading_day' => Carbon::now()->toDateString(),
                'streak' => 1,
                'longest_streak' => 1
            ]);
            $user->readingStreak()->save($streak);
        } else {
            // Update the longest_streak if streak is greater
            if ($streak->streak > $streak->longest_streak) {
                $streak->longest_streak = $streak->streak;
                $streak->save();
            }
        }

        return response()->json([
            'current_streak' => $streak->streak,
            'longest_streak' => $streak->longest_streak
        ]);
    }
}
