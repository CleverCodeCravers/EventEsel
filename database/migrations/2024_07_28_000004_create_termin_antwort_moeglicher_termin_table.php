<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('termin_antwort_moeglicher_termin', function (Blueprint $table) {
            $table->id();
            $table->foreignId('termin_antwort_id')->constrained('termin_antworten')->cascadeOnDelete();
            $table->foreignId('moeglicher_termin_id')->constrained('moegliche_termine')->cascadeOnDelete();
            $table->boolean('ist_aktiv')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('termin_antwort_moeglicher_termin');
    }
};
