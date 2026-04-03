<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Konsensteilnehmer extends Model
{
    protected $table = 'konsensteilnehmer';

    protected $fillable = ['konsensumfrage_id', 'name', 'code', 'konsensoption_id', 'hat_abgestimmt', 'ist_aktiv'];

    protected function casts(): array
    {
        return [
            'hat_abgestimmt' => 'boolean',
            'ist_aktiv' => 'boolean',
        ];
    }

    public static function booted(): void
    {
        static::creating(function (Konsensteilnehmer $teilnehmer) {
            if (empty($teilnehmer->code)) {
                $teilnehmer->code = Str::random(16);
            }
        });
    }

    public function konsensumfrage(): BelongsTo
    {
        return $this->belongsTo(Konsensumfrage::class);
    }

    public function gewaehlteOption(): BelongsTo
    {
        return $this->belongsTo(Konsensoption::class, 'konsensoption_id');
    }
}
