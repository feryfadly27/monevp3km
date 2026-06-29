<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kegiatan_berkas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained('kegiatan')->cascadeOnDelete();
            $table->enum('jenis', ['LAPORAN_KEMAJUAN', 'LAPORAN_AKHIR', 'BUKTI_LUARAN', 'LAMPIRAN']);
            $table->string('nama_file');
            $table->string('path');
            $table->unsignedBigInteger('ukuran_byte')->default(0);
            $table->foreignId('uploaded_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('uploaded_at')->useCurrent();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('kegiatan_berkas'); }
};
