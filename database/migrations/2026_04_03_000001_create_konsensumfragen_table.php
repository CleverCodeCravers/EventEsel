<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('konsensumfragen', function (Blueprint $table) {
            $table->id();
            $table->string('code', 200)->unique();
            $table->string('titel', 200);
            $table->mediumText('beschreibung')->nullable();
            $table->boolean('ist_aktiv')->default(true);
            $table->boolean('ist_abgeschlossen')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('konsensumfragen');
    }
};
