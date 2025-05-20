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
    Schema::table('pemesanans', function (Blueprint $table) {
        $table->foreignId('kursi_id')->after('jadwal_id')->constrained('kursis')->onDelete('cascade');
        $table->dropColumn('jumlah_tiket'); // karena tidak relevan jika kursi dipilih satu-satu
    });
}

public function down()
{
    Schema::table('pemesanans', function (Blueprint $table) {
        $table->dropForeign(['kursi_id']);
        $table->dropColumn('kursi_id');
        $table->integer('jumlah_tiket');
    });
}

};
