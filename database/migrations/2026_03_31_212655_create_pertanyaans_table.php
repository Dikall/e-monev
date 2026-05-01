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
        Schema::create('pertanyaans', function (Blueprint $table) {
            $table->id();

            // RELASI
            $table->foreignId('tahun_id')->constrained()->cascadeOnDelete();
            $table->foreignId('kategori_id')->constrained()->cascadeOnDelete();
            $table->foreignId('indikator_id')->constrained()->cascadeOnDelete();

            // PARENT SYSTEM
            $table->boolean('is_parent')->default(false);

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('pertanyaans')
                ->cascadeOnDelete();

             $table->enum('level', ['judul', 'subjudul', 'pertanyaan']);

            // DATA
            $table->string('nomor');
            $table->text('pertanyaan_kuisioner')->nullable();
            $table->integer('bobot')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pertanyaans');
    }
};