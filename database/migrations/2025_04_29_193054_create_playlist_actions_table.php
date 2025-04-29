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
        Schema::create('playlist_actions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['like', 'download', 'comment']);
            $table->foreignId('playlist_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('playlist_actions');
    }
};
