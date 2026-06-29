<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('penilaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('penugasan_id')->unique()->constrained('penugasan_reviewer')->cascadeOnDelete();
            $table->decimal('skor_akhir', 5, 2)->nullable();
            $table->enum('rekomendasi', ['LANJUT', 'PERBAIKAN', 'DIHENTIKAN'])->nullable();
            $table->text('catatan')->nullable();
            $table->enum('status', ['DRAFT', 'FINAL'])->default('DRAFT');
            $table->timestamp('dinilai_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('penilaian'); }
};
