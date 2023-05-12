<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = ['title', 'author', 'url', 'ratings', 'pg_id'];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'book_category');
    }

    public function finishedBooks()
    {
        return $this->belongsToMany(User::class, 'finished_books');
    }

    public function currentlyReading()
    {
        return $this->belongsToMany(User::class, 'currently_reading');
    }
    public function finishedBy()
    {
        return $this->belongsToMany(User::class, 'reading_records');
    }
}
