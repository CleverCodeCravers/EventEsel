<?php

namespace App\Http\Controllers;

use App\Models\TextoptionAntwort;
use App\Models\Textoptionenumfrage;
use Illuminate\Http\Request;

class TextoptionumfrageController extends Controller
{
    public function create()
    {
        return view('textoptionumfrage.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titel' => ['required', 'string', 'max:200'],
            'beschreibung' => ['nullable', 'string', 'max:1000'],
            'optionen' => ['required', 'array', 'min:1'],
            'optionen.*' => ['required', 'string', 'max:200'],
        ]);

        $umfrage = Textoptionenumfrage::create([
            'titel' => $request->titel,
            'beschreibung' => $request->beschreibung,
        ]);

        foreach ($request->optionen as $option) {
            if (! empty($option)) {
                $umfrage->textoptionen()->create(['text' => $option]);
            }
        }

        return redirect()->route('textoptionumfrage.create')
            ->with('success', 'Neue Textoptionenumfrage erstellt.')
            ->with('teilnahme_link', route('textoptionumfrage.show', $umfrage->code));
    }

    public function edit(Textoptionenumfrage $textoptionenumfrage)
    {
        $optionen = $textoptionenumfrage->textoptionen()->where('ist_aktiv', true)->get();

        return view('textoptionumfrage.edit', compact('textoptionenumfrage', 'optionen'));
    }

    public function update(Request $request, Textoptionenumfrage $textoptionenumfrage)
    {
        $request->validate([
            'titel' => ['required', 'string', 'max:200'],
            'beschreibung' => ['nullable', 'string', 'max:1000'],
            'bestehende_optionen' => ['nullable', 'array'],
            'bestehende_optionen.*' => ['integer', 'exists:textoptionen,id'],
            'neue_optionen' => ['nullable', 'array'],
            'neue_optionen.*' => ['nullable', 'string', 'max:200'],
        ]);

        $textoptionenumfrage->update([
            'titel' => $request->titel,
            'beschreibung' => $request->beschreibung,
        ]);

        // Entfernte Optionen löschen (Cascade löscht Antworten)
        $bestehendeIds = $request->input('bestehende_optionen', []);
        $textoptionenumfrage->textoptionen()
            ->whereNotIn('id', $bestehendeIds)
            ->delete();

        // Neue Optionen hinzufügen
        foreach ($request->input('neue_optionen', []) as $text) {
            if (! empty($text)) {
                $textoptionenumfrage->textoptionen()->create(['text' => $text]);
            }
        }

        return redirect()->route('dashboard')->with('success', 'Textoptionumfrage aktualisiert.');
    }

    public function show(string $code)
    {
        $umfrage = Textoptionenumfrage::where('code', $code)->firstOrFail();
        $optionen = $umfrage->textoptionen()->where('ist_aktiv', true)->get();

        $antworten = [];
        foreach ($optionen as $option) {
            foreach ($option->antworten()->where('ist_aktiv', true)->get() as $antwort) {
                $antworten[$antwort->teilnehmer][] = $option->id;
            }
        }

        return view('textoptionumfrage.show', compact('umfrage', 'optionen', 'antworten'));
    }

    public function vote(Request $request, string $code)
    {
        $umfrage = Textoptionenumfrage::where('code', $code)->firstOrFail();

        if ($umfrage->ist_abgeschlossen) {
            return redirect()->route('textoptionumfrage.show', $code)
                ->withErrors(['umfrage' => 'Diese Umfrage ist abgeschlossen.']);
        }

        $request->validate([
            'teilnehmer' => ['required', 'string', 'max:200'],
            'optionen' => ['nullable', 'array'],
            'optionen.*' => ['integer', 'exists:textoptionen,id'],
        ]);

        if ($request->has('optionen')) {
            foreach ($request->optionen as $optionId) {
                TextoptionAntwort::create([
                    'textoption_id' => $optionId,
                    'teilnehmer' => $request->teilnehmer,
                ]);
            }
        }

        return redirect()->route('textoptionumfrage.show', $code)
            ->with('success', 'Vielen Dank für deine Abstimmung!');
    }
}
