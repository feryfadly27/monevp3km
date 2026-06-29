<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kegiatan_anggota', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kegiatan_id')->constrained('kegiatan')->cascadeOnDelete();
            $table->foreignId('dosen_id')->constrained('dosen')->restrictOnDelete();
            $table->string('peran')->default('Anggota');
            $table->timestamps();
            $table->unique(['kegiatan_id', 'dosen_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('kegiatan_anggota'); }
};
