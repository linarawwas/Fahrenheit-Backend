<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveBookIdFromSecretAtticsTable extends Migration
{
    public function up()
    {
        Schema::table('secret_attics', function (Blueprint $table) {
            $table->dropForeign(['book_id']);  // Remove foreign key constraint
            $table->dropColumn('book_id');  // Remove the column
        });
    }

    public function down()
    {
        Schema::table('secret_attics', function (Blueprint $table) {
            $table->foreignId('book_id')->nullable()->constrained('books')->onDelete('cascade');
        });
    }
}
