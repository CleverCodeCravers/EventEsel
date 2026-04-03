<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TerminAntwort extends Model
{
    protected $table = 'termin_antworten';

    protected $fillable = ['teilnehmer', 'ist_aktiv'];

    protected function casts(): array
    {
        return [
            'ist_aktiv' => 'boolean',
        ];
    }

    public function moeglicheTermine(): BelongsToMany
    {
        return $this->belongsToMany(MoeglicherTermin::class, 'termin_antwort_moeglicher_termin');
    }
}
