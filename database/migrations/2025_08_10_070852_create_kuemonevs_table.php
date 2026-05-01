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
        Schema::create('kuemonevs', function (Blueprint $table) {
            $table->id(); // bigint unsigned, auto increment, primary key
            $table->string('file_name', 255); // varchar(255), not null
            $table->binary('file_data')->nullable();
            $table->timestamps(); // created_at & updated_at (timestamp nullable)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kuemonevs');
    }
};
