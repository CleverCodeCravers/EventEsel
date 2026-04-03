<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MoeglicherTermin extends Model
{
    protected $table = 'moegliche_termine';

    protected $fillable = ['terminumfrage_id', 'datum', 'ist_aktiv'];

    protected function casts(): array
    {
        return [
            'datum' => 'datetime',
            'ist_aktiv' => 'boolean',
        ];
    }

    public function terminumfrage(): BelongsTo
    {
        return $this->belongsTo(Terminumfrage::class);
    }

    public function terminAntworten(): BelongsToMany
    {
        return $this->belongsToMany(TerminAntwort::class, 'termin_antwort_moeglicher_termin');
    }
}
