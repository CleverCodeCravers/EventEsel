<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('konsensteilnehmer', function (Blueprint $table) {
            $table->id();
            $table->foreignId('konsensumfrage_id')->constrained('konsensumfragen')->cascadeOnDelete();
            $table->string('name', 200);
            $table->string('code', 200)->unique();
            $table->foreignId('konsensoption_id')->nullable()->constrained('konsensoptionen')->nullOnDelete();
            $table->boolean('hat_abgestimmt')->default(false);
            $table->boolean('ist_aktiv')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('konsensteilnehmer');
    }
};
