<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TextoptionAntwort extends Model
{
    protected $table = 'textoption_antworten';

    protected $fillable = ['textoption_id', 'teilnehmer', 'ist_aktiv'];

    protected function casts(): array
    {
        return [
            'ist_aktiv' => 'boolean',
        ];
    }

    public function textoption(): BelongsTo
    {
        return $this->belongsTo(Textoption::class);
    }
}
