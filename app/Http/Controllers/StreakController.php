<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class StreakController extends Controller
{
    public function getStreak(Request $request)
    {
        $user = Auth::user();
        $streak = $user->readingStreaks()->latest()->first();
        $currentStreak = 0;
        $longestStreak = $streak->longest_streak;
    
        // check if there is a streak
        if ($streak) {
            $lastReadingDay = Carbon::parse($streak->last_reading_day);
    
            // check if last reading day is yesterday
            if ($lastReadingDay->eq(Carbon::yesterday())) {
                $currentStreak = $streak->streak + 1;
            }
        }
    
        // update the streak table with new reading day
        $user->readingStreaks()->updateOrCreate(
            ['id' => $streak ? $streak->id : null],
            [
                'last_reading_day' => Carbon::now()->toDateString(),
                'streak' => $currentStreak,
                'longest_streak' => max($currentStreak, $longestStreak)
            ]
        );
    
        return response()->json([
            'message' => 'Streak updated!',
            'current_streak' => $currentStreak,
            'longest_streak' => max($currentStreak, $longestStreak)
        ]);
    }
    
}
