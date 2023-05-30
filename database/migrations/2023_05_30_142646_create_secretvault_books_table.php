<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('secretvault_books', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('secret_vault_id');
            $table->unsignedBigInteger('secret_book_id');
            // Other columns for the pivot table, if any
            $table->timestamps();
    
            $table->foreign('secret_vault_id')->references('id')->on('secret_vaults')->onDelete('cascade');
            $table->foreign('secret_book_id')->references('id')->on('secret_books')->onDelete('cascade');
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('secretvault_books');
    }
};
