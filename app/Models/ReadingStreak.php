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
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function incrementStreak($user_id) {
        $streak = self::where('user_id', $user_id)->value('streak');
        $streak++;
        self::where('user_id', $user_id)->update(['streak' => $streak]);
        $user = User::findOrFail($user_id);
        $user->updateRankIfStreakIsMultipleOfFive($streak);
    }
    
    
}
