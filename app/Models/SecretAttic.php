<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecretAttic extends Model
{
    protected $fillable = ['user_id'];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function books()
    {
        return $this->belongsToMany(Book::class, 'book_secret_attic')
            ->withTimestamps();
    }
}
