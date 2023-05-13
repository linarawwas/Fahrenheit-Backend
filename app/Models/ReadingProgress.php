<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ReadingProgress extends Model
{
    use HasFactory;

    protected $fillable = [
        'started_reading_at',
        'finished_reading_at',
        'reading_duration',
    ];

    protected $dates = [
        'started_reading_at',
        'finished_reading_at',
        'reading_duration',
    ];

    protected $appends = [
        'reading_duration_in_days'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function getReadingDurationInDaysAttribute()
    {
        $startedAt = Carbon::parse($this->started_reading_at);
        $finishedAt = Carbon::parse($this->finished_reading_at);
        return $startedAt->diffInDays($finishedAt);
    }
}
