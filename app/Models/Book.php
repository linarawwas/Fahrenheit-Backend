<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = ['pg_id', 'title', 'author', 'language', 'url', 'ratings'];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'book_category');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'reading_progress');
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function getReadingDurationInDaysAttribute()
    {
        $startedAt = Carbon::parse($this->started_reading_at);
        $finishedAt = Carbon::parse($this->finished_reading_at);
        return $startedAt->diffInDays($finishedAt);
    }

    public function secretAttics()
    {
        return $this->belongsToMany(SecretAttic::class, 'book_secret_attic')->withTimestamps();
    }
}
