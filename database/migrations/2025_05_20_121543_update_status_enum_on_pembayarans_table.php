<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // Ubah enum menjadi string sementara
        DB::statement("ALTER TABLE pembayarans MODIFY status VARCHAR(20)");

        // Update data agar aman (jika perlu)
        DB::table('pembayarans')->whereNull('status')->update(['status' => 'waiting']);

        // Lalu ubah ke ENUM baru
        DB::statement("ALTER TABLE pembayarans MODIFY status ENUM('waiting', 'pending', 'paid') DEFAULT 'waiting'");
    }

    public function down(): void
    {
        // Rollback ke enum lama
        DB::statement("ALTER TABLE pembayarans MODIFY status VARCHAR(20)");
        DB::statement("ALTER TABLE pembayarans MODIFY status ENUM('pending', 'paid') DEFAULT 'pending'");
    }
};
