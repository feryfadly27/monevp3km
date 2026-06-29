<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kegiatan_luaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained('kegiatan')->cascadeOnDelete();
            $table->foreignId('skema_luaran_id')->nullable()->constrained('skema_luaran')->nullOnDelete();
            $table->enum('jenis', ['PUBLIKASI', 'HKI', 'PRODUK', 'LAPORAN', 'LAINNYA']);
            $table->string('judul_luaran');
            $table->string('url_bukti')->nullable();
            $table->enum('status_capaian', ['RENCANA', 'PROSES', 'TERCAPAI'])->default('RENCANA');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('kegiatan_luaran'); }
};
