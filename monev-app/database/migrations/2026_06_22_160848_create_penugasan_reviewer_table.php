<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('penugasan_reviewer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained('kegiatan')->cascadeOnDelete();
            $table->foreignId('reviewer_user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('assigned_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('assigned_at')->useCurrent();
            $table->enum('status', ['MENUNGGU', 'DALAM_PENILAIAN', 'SELESAI'])->default('MENUNGGU');
            $table->timestamps();
            $table->unique(['kegiatan_id', 'reviewer_user_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('penugasan_reviewer'); }
};
