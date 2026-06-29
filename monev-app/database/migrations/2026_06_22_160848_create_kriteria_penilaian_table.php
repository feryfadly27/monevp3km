<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kriteria_penilaian', function (Blueprint $table) {
            $table->id();
            $table->enum('scope', ['GLOBAL', 'KATEGORI', 'SKEMA'])->default('GLOBAL');
            $table->foreignId('kategori_id')->nullable()->constrained('kategori')->nullOnDelete();
            $table->foreignId('skema_id')->nullable()->constrained('skema')->nullOnDelete();
            $table->string('nama');
            $table->decimal('bobot', 5, 2);
            $table->unsignedTinyInteger('skor_min')->default(1);
            $table->unsignedTinyInteger('skor_max')->default(100);
            $table->unsignedTinyInteger('urutan')->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('kriteria_penilaian'); }
};
