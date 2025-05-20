<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeTotalHargaToBigintOnPemesanansTable extends Migration
{
    public function up()
    {
        Schema::table('pemesanans', function (Blueprint $table) {
            // Ubah kolom total_harga jadi bigint (integer besar)
            $table->bigInteger('total_harga')->change();
        });
    }

    public function down()
    {
        Schema::table('pemesanans', function (Blueprint $table) {
            // Kembalikan ke decimal(10,2)
            $table->decimal('total_harga', 10, 2)->change();
        });
    }
}
