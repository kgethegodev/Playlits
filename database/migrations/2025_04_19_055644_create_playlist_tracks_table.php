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
        Schema::create('playlist_tracks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('artist');
            $table->time('duration');
            $table->string('external_id')->nullable();
            $table->foreignId('playlist_id')->constrained('playlists');
            $table->enum('status', ['created', 'found', 'not_found', 'added'])->default('created');
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['playlist_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('playlist_tracks');
    }
};
