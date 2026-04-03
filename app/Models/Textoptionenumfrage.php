<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Textoptionenumfrage extends Model
{
    protected $table = 'textoptionenumfragen';

    protected $fillable = ['code', 'titel', 'beschreibung', 'ist_aktiv', 'ist_abgeschlossen'];

    protected function casts(): array
    {
        return [
            'ist_aktiv' => 'boolean',
            'ist_abgeschlossen' => 'boolean',
        ];
    }

    public static function booted(): void
    {
        static::creating(function (Textoptionenumfrage $umfrage) {
            if (empty($umfrage->code)) {
                $umfrage->code = Str::random(16);
            }
        });
    }

    public function textoptionen(): HasMany
    {
        return $this->hasMany(Textoption::class);
    }
}
