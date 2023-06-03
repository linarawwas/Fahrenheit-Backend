<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReceivedColumnToBookSecretAtticTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('book_secret_attic', function (Blueprint $table) {
            $table->boolean('received')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('book_secret_attic', function (Blueprint $table) {
            $table->dropColumn('received');
        });
    }
}
