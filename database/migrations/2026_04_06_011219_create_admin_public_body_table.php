<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('admin_public_body', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('public_body_id')
                ->constrained('public_bodies')
                ->onDelete('cascade');

            $table->timestamps();

            $table->unique(['user_id', 'public_body_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admin_public_body');
    }
};
