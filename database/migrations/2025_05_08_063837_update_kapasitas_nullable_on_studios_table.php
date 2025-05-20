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
    Schema::table('studios', function (Blueprint $table) {
        $table->integer('kapasitas')->nullable()->change();
    });
}

public function down()
{
    Schema::table('studios', function (Blueprint $table) {
        $table->integer('kapasitas')->nullable(false)->change();
    });
}

};
