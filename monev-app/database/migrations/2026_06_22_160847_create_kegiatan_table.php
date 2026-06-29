<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kegiatan', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->foreignId('skema_id')->constrained('skema')->restrictOnDelete();
            $table->foreignId('kategori_id')->constrained('kategori')->restrictOnDelete();
            $table->unsignedSmallInteger('tahun');
            $table->foreignId('ketua_dosen_id')->constrained('dosen')->restrictOnDelete();
            $table->string('sumber_dana')->nullable();
            $table->decimal('jumlah_dana', 15, 2)->default(0);
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->enum('status', ['TERDAFTAR', 'BERJALAN', 'LAPORAN_MASUK', 'DINILAI', 'SELESAI'])->default('TERDAFTAR');
            $table->decimal('skor_final', 5, 2)->nullable();
            $table->enum('rekomendasi_final', ['LANJUT', 'PERBAIKAN', 'DIHENTIKAN'])->nullable();
            $table->text('catatan_admin')->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->softDeletes();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('kegiatan'); }
};
