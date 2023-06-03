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
        Schema::table('secret_attics', function (Blueprint $table) {
            $table->boolean('received')->default(false);
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('secret_attics', function (Blueprint $table) {
            $table->dropColumn('received');
        });
    }
    
};
