<?php

namespace App\Http\Controllers;

use App\Models\MoeglicherTermin;
use App\Models\TerminAntwort;
use App\Models\Terminumfrage;
use Illuminate\Http\Request;

class TerminumfrageController extends Controller
{
    public function create()
    {
        return view('terminumfrage.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'titel' => ['required', 'string', 'max:200'],
            'beschreibung' => ['nullable', 'string', 'max:16777215'],
            'termine' => ['required', 'array', 'min:1'],
            'termine.*' => ['required', 'date'],
        ]);

        $umfrage = Terminumfrage::create([
            'titel' => $request->titel,
            'beschreibung' => $request->beschreibung,
        ]);

        foreach ($request->termine as $termin) {
            if (! empty($termin)) {
                $umfrage->moeglicheTermine()->create(['datum' => $termin]);
            }
        }

        return redirect()->route('terminumfrage.create')
            ->with('success', 'Neue Terminumfrage erstellt.')
            ->with('teilnahme_link', route('terminumfrage.show', $umfrage->code));
    }

    public function show(string $code)
    {
        $umfrage = Terminumfrage::where('code', $code)->firstOrFail();
        $termine = $umfrage->moeglicheTermine()->orderBy('datum')->get();

        $antworten = [];
        foreach ($termine as $termin) {
            foreach ($termin->terminAntworten as $antwort) {
                $antworten[$antwort->teilnehmer][$termin->id] = true;
            }
        }

        return view('terminumfrage.show', compact('umfrage', 'termine', 'antworten'));
    }

    public function vote(Request $request, string $code)
    {
        $umfrage = Terminumfrage::where('code', $code)->firstOrFail();

        $request->validate([
            'teilnehmer' => ['required', 'string', 'max:200'],
            'termine' => ['nullable', 'array'],
            'termine.*' => ['integer', 'exists:moegliche_termine,id'],
        ]);

        $antwort = TerminAntwort::create([
            'teilnehmer' => $request->teilnehmer,
        ]);

        if ($request->has('termine')) {
            $antwort->moeglicheTermine()->attach($request->termine);
        }

        return redirect()->route('terminumfrage.show', $code)
            ->with('success', 'Vielen Dank für deine Abstimmung!');
    }
}
