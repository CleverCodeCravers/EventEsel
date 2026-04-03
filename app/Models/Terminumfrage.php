<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Terminumfrage extends Model
{
    protected $table = 'terminumfragen';

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
        static::creating(function (Terminumfrage $umfrage) {
            if (empty($umfrage->code)) {
                $umfrage->code = Str::random(16);
            }
        });
    }

    public function moeglicheTermine(): HasMany
    {
        return $this->hasMany(MoeglicherTermin::class);
    }
}
