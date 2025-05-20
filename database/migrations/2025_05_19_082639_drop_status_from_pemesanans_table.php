<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropStatusFromPemesanansTable extends Migration
{
    /**
     * Run the migrations: hapus kolom status.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pemesanans', function (Blueprint $table) {
            if (Schema::hasColumn('pemesanans', 'status')) {
                $table->dropColumn('status');
            }
        });
    }

    /**
     * Reverse the migrations: kembalikan kolom status.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pemesanans', function (Blueprint $table) {
            // enum hanya pending dan paid (atau confirmed jika itu yang digunakan)
            $table->enum('status', ['pending', 'confirmed'])
                    ->default('pending')
                    ->after('total_harga');
        });
    }
}
