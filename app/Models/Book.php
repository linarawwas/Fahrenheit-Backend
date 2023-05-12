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
    public function users()
    {
        return $this->belongsToMany(User::class, 'reading_progress')
                    ->withPivot('started_at', 'finished_at');
    }

    public function notes()
    {
        return $this->hasMany(Note::class);
    }
    
    
}
