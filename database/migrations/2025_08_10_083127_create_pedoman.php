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
        Schema::create('pedomen', function (Blueprint $table) {
            $table->id();
            $table->string('file_name'); // Nama file
            $table->binary('file_data'); // Data file dalam bentuk biner
            $table->timestamps();
        });

        Schema::table('pedomen', function (Blueprint $table) {
            DB::statement("ALTER TABLE pedomen MODIFY file_data LONGBLOB");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedomen');
    }
};
