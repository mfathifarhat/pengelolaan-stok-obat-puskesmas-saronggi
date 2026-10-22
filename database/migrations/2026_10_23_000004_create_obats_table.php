<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('obats', function (Blueprint $table) {
            $table->id();
            $table->string('kode_obat')->unique();
            $table->string('nama_obat');
            $table->foreignId('kategori_obat_id')->constrained('kategori_obats')->cascadeOnDelete();
            $table->string('satuan'); // tablet, kapsul, botol, ampul, strip, fls, sachet, tube, dll
            $table->string('produsen')->nullable(); // Kimia Farma, Kalbe Farma, Sanbe, dll
            $table->integer('stok_minimum')->default(10);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('obats');
    }
};
