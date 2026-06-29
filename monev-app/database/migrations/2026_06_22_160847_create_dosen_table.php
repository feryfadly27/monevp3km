<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('dosen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('prodi_id')->constrained('prodi')->restrictOnDelete();
            $table->string('nidn', 20)->unique();
            $table->string('nama');
            $table->string('email')->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('dosen'); }
};
