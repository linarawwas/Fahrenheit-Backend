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
        Schema::table('reading_streaks', function (Blueprint $table) {
            $table->integer('longest_streak')->default(0);
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('reading_streaks', function (Blueprint $table) {
            $table->dropColumn('longest_streak');
        });
    }
};
