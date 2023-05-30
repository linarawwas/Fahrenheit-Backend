<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookSecretAtticTable extends Migration
{
    public function up()
    {
        Schema::create('book_secret_attic', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('secret_attic_id');
            $table->unsignedBigInteger('book_id');
            $table->timestamps();

            $table->foreign('secret_attic_id')->references('id')->on('secret_attics')->onDelete('cascade');
            $table->foreign('book_id')->references('id')->on('books')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('book_secret_attic');
    }
}
