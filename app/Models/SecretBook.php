<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecretBook extends Model
{
    // Other attributes and methods for the SecretBook model
    protected $fillable = [
        'pg_id',
        'title',
        'author',
        'language',
        'url',
        'ratings',
        'price',
    ];

    public function secretVaults()
    {
        return $this->belongsToMany(SecretVault::class, 'secretvault_books');
    }
}
