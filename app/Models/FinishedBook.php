<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinishedBook extends Model
{
    use HasFactory;

    protected $fillable = [
        'book_id',
        'finished_at',
        'reading_record_id',
    ];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function readingRecord()
    {
        return $this->belongsTo(ReadingRecord::class);
    }
}
