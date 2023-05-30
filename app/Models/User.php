<?php
namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['username', 'password', 'email', 'picture', 'profile_picture'];

    protected $attributes = [
        'reading_rank' => 0,
        'current_reading_streak' => 1,
        'longest_reading_streak' => 1,
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function readingStreak()
    {
        return $this->hasOne(ReadingStreak::class);
    }
    

    public function books()
    {
        return $this->belongsToMany(Book::class, 'reading_progress');
    }

    public function readingProgress()
    {
        return $this->hasMany(ReadingProgress::class);
    }

    public function increaseReadingRankOnStart($book)
    {
        $this->reading_rank += 1;
        $this->save();

        return $this;
    }

    public function increaseReadingRankOnFinish($book)
    {
        $this->reading_rank += 2;
        $this->save();

        return $this;
    }

    public function updateRankIfStreakIsMultipleOfFive($streak)
    {
        if ($streak % 5 === 0) {
            $this->reading_rank += 3;
            $this->save();
        }
    }

    public function secretAttic()
    {
        return $this->hasOne(SecretAttic::class, 'user_id');
    }
}
