<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kegiatan_status_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained('kegiatan')->cascadeOnDelete();
            $table->enum('status_lama', ['TERDAFTAR', 'BERJALAN', 'LAPORAN_MASUK', 'DINILAI', 'SELESAI'])->nullable();
            $table->enum('status_baru', ['TERDAFTAR', 'BERJALAN', 'LAPORAN_MASUK', 'DINILAI', 'SELESAI']);
            $table->foreignId('oleh_user_id')->constrained('users')->restrictOnDelete();
            $table->text('catatan')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }
    public function down(): void { Schema::dropIfExists('kegiatan_status_log'); }
};
