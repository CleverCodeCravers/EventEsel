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
