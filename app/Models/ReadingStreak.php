<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReadingStreak extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'last_reading_day',
        'streak',
        'longest_streak'=>1,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function incrementStreak($user_id)
    {
        $streak = self::where('user_id', $user_id)->value('streak');
        $longestStreak = self::where('user_id', $user_id)->value('longest_streak');
        $streak++;

        if ($streak > $longestStreak) {
            $longestStreak = $streak;
        }

        self::where('user_id', $user_id)->update([
            'streak' => $streak,
            'longest_streak' => $longestStreak,
        ]);

        $user = User::findOrFail($user_id);
        $user->updateRankIfStreakIsMultipleOfFive($streak);
    }
}
