<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Konsensoption extends Model
{
    protected $table = 'konsensoptionen';

    protected $fillable = ['konsensumfrage_id', 'text', 'ist_aktiv'];

    protected function casts(): array
    {
        return [
            'ist_aktiv' => 'boolean',
        ];
    }

    public function konsensumfrage(): BelongsTo
    {
        return $this->belongsTo(Konsensumfrage::class);
    }

    public function stimmen(): HasMany
    {
        return $this->hasMany(Konsensteilnehmer::class, 'konsensoption_id');
    }
}
