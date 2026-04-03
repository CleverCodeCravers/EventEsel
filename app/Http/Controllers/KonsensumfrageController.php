<?php

namespace App\Http\Controllers;

use App\Models\Konsensumfrage;
use App\Models\Konsensteilnehmer;
use Illuminate\Http\Request;

class KonsensumfrageController extends Controller
{
    public function create()
    {
        return view('konsensumfrage.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titel' => ['required', 'string', 'max:200'],
            'beschreibung' => ['nullable', 'string', 'max:16777215'],
            'optionen' => ['required', 'array', 'min:2'],
            'optionen.*' => ['required', 'string', 'max:200'],
            'teilnehmer' => ['required', 'array', 'min:2'],
            'teilnehmer.*' => ['required', 'string', 'max:200'],
        ]);

        // Eindeutigkeit der Teilnehmer-Namen prüfen
        if (count($request->teilnehmer) !== count(array_unique($request->teilnehmer))) {
            return back()->withErrors(['teilnehmer' => 'Teilnehmer-Namen müssen eindeutig sein.'])->withInput();
        }

        $umfrage = Konsensumfrage::create([
            'titel' => $request->titel,
            'beschreibung' => $request->beschreibung,
        ]);

        foreach ($request->optionen as $option) {
            if (! empty($option)) {
                $umfrage->optionen()->create(['text' => $option]);
            }
        }

        foreach ($request->teilnehmer as $name) {
            if (! empty($name)) {
                $umfrage->teilnehmer()->create(['name' => $name]);
            }
        }

        return redirect()->route('konsensumfrage.links', $umfrage)
            ->with('success', 'Konsensumfrage erstellt. Verteile die Links an die Teilnehmer.');
    }

    public function links(Konsensumfrage $konsensumfrage)
    {
        $teilnehmer = $konsensumfrage->teilnehmer()->where('ist_aktiv', true)->get();

        return view('konsensumfrage.links', compact('konsensumfrage', 'teilnehmer'));
    }

    public function show(string $code, string $teilnehmerCode)
    {
        $umfrage = Konsensumfrage::where('code', $code)->firstOrFail();
        $teilnehmer = Konsensteilnehmer::where('code', $teilnehmerCode)
            ->where('konsensumfrage_id', $umfrage->id)
            ->where('ist_aktiv', true)
            ->firstOrFail();

        $optionen = $umfrage->optionen()->where('ist_aktiv', true)->get();
        $totalTeilnehmer = $umfrage->teilnehmer()->where('ist_aktiv', true)->count();
        $abgestimmt = $umfrage->teilnehmer()->where('ist_aktiv', true)->where('hat_abgestimmt', true)->count();

        // Stimmen pro Option zählen
        $ergebnisse = $optionen->map(function ($option) use ($totalTeilnehmer) {
            $stimmen = Konsensteilnehmer::where('konsensoption_id', $option->id)
                ->where('hat_abgestimmt', true)
                ->count();
            return [
                'id' => $option->id,
                'text' => $option->text,
                'stimmen' => $stimmen,
                'prozent' => $totalTeilnehmer > 0 ? round(($stimmen / $totalTeilnehmer) * 100) : 0,
            ];
        });

        return view('konsensumfrage.show', compact('umfrage', 'teilnehmer', 'optionen', 'ergebnisse', 'totalTeilnehmer', 'abgestimmt'));
    }

    public function vote(Request $request, string $code, string $teilnehmerCode)
    {
        $umfrage = Konsensumfrage::where('code', $code)->firstOrFail();
        $teilnehmer = Konsensteilnehmer::where('code', $teilnehmerCode)
            ->where('konsensumfrage_id', $umfrage->id)
            ->where('ist_aktiv', true)
            ->firstOrFail();

        if ($umfrage->ist_abgeschlossen) {
            return redirect()->route('konsensumfrage.show', [$code, $teilnehmerCode])
                ->withErrors(['umfrage' => 'Diese Umfrage ist abgeschlossen.']);
        }

        if ($teilnehmer->hat_abgestimmt) {
            return redirect()->route('konsensumfrage.show', [$code, $teilnehmerCode])
                ->withErrors(['umfrage' => 'Du hast bereits abgestimmt.']);
        }

        $request->validate([
            'option' => ['required', 'integer', 'exists:konsensoptionen,id'],
        ]);

        $teilnehmer->update([
            'konsensoption_id' => $request->option,
            'hat_abgestimmt' => true,
        ]);

        return redirect()->route('konsensumfrage.show', [$code, $teilnehmerCode])
            ->with('success', 'Deine Stimme wurde abgegeben. Vielen Dank!');
    }

    public function edit(Konsensumfrage $konsensumfrage)
    {
        $optionen = $konsensumfrage->optionen()->where('ist_aktiv', true)->get();
        $teilnehmer = $konsensumfrage->teilnehmer()->where('ist_aktiv', true)->get();

        return view('konsensumfrage.edit', compact('konsensumfrage', 'optionen', 'teilnehmer'));
    }

    public function update(Request $request, Konsensumfrage $konsensumfrage)
    {
        $request->validate([
            'titel' => ['required', 'string', 'max:200'],
            'beschreibung' => ['nullable', 'string', 'max:16777215'],
            'bestehende_optionen' => ['nullable', 'array'],
            'bestehende_optionen.*' => ['integer', 'exists:konsensoptionen,id'],
            'neue_optionen' => ['nullable', 'array'],
            'neue_optionen.*' => ['nullable', 'string', 'max:200'],
            'bestehende_teilnehmer' => ['nullable', 'array'],
            'bestehende_teilnehmer.*' => ['integer', 'exists:konsensteilnehmer,id'],
            'neue_teilnehmer' => ['nullable', 'array'],
            'neue_teilnehmer.*' => ['nullable', 'string', 'max:200'],
        ]);

        $konsensumfrage->update([
            'titel' => $request->titel,
            'beschreibung' => $request->beschreibung,
        ]);

        // Optionen synchronisieren
        $bestehendeOptionen = $request->input('bestehende_optionen', []);
        $konsensumfrage->optionen()->whereNotIn('id', $bestehendeOptionen)->delete();

        foreach ($request->input('neue_optionen', []) as $text) {
            if (! empty($text)) {
                $konsensumfrage->optionen()->create(['text' => $text]);
            }
        }

        // Teilnehmer synchronisieren (nur nicht-abgestimmte entfernen)
        $bestehendeTeilnehmer = $request->input('bestehende_teilnehmer', []);
        $konsensumfrage->teilnehmer()
            ->whereNotIn('id', $bestehendeTeilnehmer)
            ->where('hat_abgestimmt', false)
            ->delete();

        foreach ($request->input('neue_teilnehmer', []) as $name) {
            if (! empty($name)) {
                $konsensumfrage->teilnehmer()->create(['name' => $name]);
            }
        }

        return redirect()->route('dashboard')->with('success', 'Konsensumfrage aktualisiert.');
    }
}
