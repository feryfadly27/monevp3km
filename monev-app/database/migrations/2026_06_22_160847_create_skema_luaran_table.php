<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('skema_luaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('skema_id')->constrained('skema')->cascadeOnDelete();
            $table->enum('jenis', ['PUBLIKASI', 'HKI', 'PRODUK', 'LAPORAN', 'LAINNYA']);
            $table->string('deskripsi');
            $table->boolean('wajib')->default(true);
            $table->unsignedSmallInteger('jumlah_minimal')->default(1);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('skema_luaran'); }
};
