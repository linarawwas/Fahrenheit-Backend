<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('secret_books', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pg_id');
            $table->string('title');
            $table->string('author');
            $table->string('language')->default('Eng');
            $table->string('url');
            $table->float('ratings')->default(0);
            $table->integer('price')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('secret_books');
    }
};
