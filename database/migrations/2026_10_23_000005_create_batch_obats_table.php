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
        Schema::create('batch_obats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('obat_id')->constrained('obats')->cascadeOnDelete();
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->string('no_batch');
            $table->date('tanggal_kadaluarsa');
            $table->date('tanggal_masuk');
            $table->decimal('harga_beli', 12, 2)->default(0);
            $table->integer('stok')->default(0);
            $table->timestamps();

            $table->index(['obat_id', 'tanggal_kadaluarsa']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batch_obats');
    }
};
