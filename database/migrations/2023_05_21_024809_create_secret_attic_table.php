<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSecretAtticsTable extends Migration
{
    public function up()
    {
        Schema::create('secret_attics', function (Blueprint $table) {
            $table->id();
            // $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('user_id')->unique();
            $table->foreignId('book_id')->nullable()->constrained('books')->onDelete('cascade');

            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('secret_attics');
    }
}
