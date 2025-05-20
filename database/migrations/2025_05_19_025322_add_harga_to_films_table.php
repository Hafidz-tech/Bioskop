<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('films', function (Blueprint $table) {
            $table->decimal('harga', 10, 2)
                  ->after('durasi')
                  ->default(0)
                  ->comment('Harga tiket/film');
        });
    }

    public function down(): void
    {
        Schema::table('films', function (Blueprint $table) {
            $table->dropColumn('harga');
        });
    }
};
