<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pemesanans', function (Blueprint $table) {
            // Hapus foreign key constraint dulu
            $table->dropForeign(['kursi_id']);

            // Baru hapus kolom
            $table->dropColumn('kursi_id');
        });
    }

    public function down(): void
    {
        Schema::table('pemesanans', function (Blueprint $table) {
            $table->unsignedBigInteger('kursi_id')->nullable();

            // Tambahkan kembali foreign key jika diperlukan
            $table->foreign('kursi_id')->references('id')->on('kursis')->onDelete('cascade');
        });
    }
};
