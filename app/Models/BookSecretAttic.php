<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class BookSecretAttic extends Pivot
{
    protected $table = 'book_secret_attic';

    protected $fillable = [
        'secret_attic_id',
        'book_id'
    ];

    // Define any relationships or additional methods if needed
}
