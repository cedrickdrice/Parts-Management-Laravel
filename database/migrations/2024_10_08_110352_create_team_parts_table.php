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
        Schema::create('team_parts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->onDelete('cascade');
            $table->foreignId('part_id')->constrained()->onDelete('cascade');
            $table->decimal('multiplier', 8, 4)->nullable();
            $table->decimal('static_price', 8, 2)->nullable();
            $table->decimal('team_price', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('team_parts');
    }
};
