<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrentlyReading extends Model
{
    protected $fillable = ['user_id', 'book_id', 'started_reading_at'];
}

