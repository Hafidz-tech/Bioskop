<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('films', function (Blueprint $table) {
            // ubah kolom harga jadi integer
            $table->integer('harga')->default(0)->change();
        });
    }

    public function down()
    {
        Schema::table('films', function (Blueprint $table) {
            // rollback ke decimal(10,2)
            $table->decimal('harga', 10, 2)->default(0.00)->change();
        });
    }
};
