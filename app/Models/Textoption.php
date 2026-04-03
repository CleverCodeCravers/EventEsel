<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Textoption extends Model
{
    protected $table = 'textoptionen';

    protected $fillable = ['textoptionenumfrage_id', 'text', 'ist_aktiv'];

    protected function casts(): array
    {
        return [
            'ist_aktiv' => 'boolean',
        ];
    }

    public function textoptionenumfrage(): BelongsTo
    {
        return $this->belongsTo(Textoptionenumfrage::class);
    }

    public function antworten(): HasMany
    {
        return $this->hasMany(TextoptionAntwort::class);
    }
}
