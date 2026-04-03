<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('konsensoptionen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('konsensumfrage_id')->constrained('konsensumfragen')->cascadeOnDelete();
            $table->string('text', 200);
            $table->boolean('ist_aktiv')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('konsensoptionen');
    }
};
