<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReadingDurationToReadingProgress extends Migration
{
    public function up()
    {
        Schema::table('reading_progress', function (Blueprint $table) {
            $table->integer('reading_duration')->nullable();
        });
    }

    public function down()
    {
        Schema::table('reading_progress', function (Blueprint $table) {
            $table->dropColumn('reading_duration');
        });
    }
}
