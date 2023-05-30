<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Pivot;

class SecretVaultBooks extends Pivot
{
    protected $table = 'secretvault_books';

    protected $fillable = [
        'secret_vault_id',
        'secret_book_id'
    ];
}
