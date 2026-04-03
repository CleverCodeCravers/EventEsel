<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('moegliche_termine', function (Blueprint $table) {
            $table->id();
            $table->foreignId('terminumfrage_id')->constrained('terminumfragen')->cascadeOnDelete();
            $table->dateTime('datum')->nullable();
            $table->boolean('ist_aktiv')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('moegliche_termine');
    }
};
