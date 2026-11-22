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
        Schema::create('round_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('round_id')->constrained()->cascadeOnDelete();
            $table->string('card', 3);
            $table->unsignedTinyInteger('dealt_to_position');
            $table->unsignedTinyInteger('held_by_position');
            $table->unsignedTinyInteger('passed_to_position')->nullable();
            $table->foreignId('trick_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('play_order')->nullable();
            $table->timestamps();

            $table->unique(['round_id', 'card']);
            $table->index(['round_id', 'held_by_position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('round_cards');
    }
};
