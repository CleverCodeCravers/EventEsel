<?php

namespace App\Http\Controllers;

use App\Models\Terminumfrage;
use App\Models\Textoptionenumfrage;

class DashboardController extends Controller
{
    public function index()
    {
        $terminumfragen = Terminumfrage::withCount(['moeglicheTermine as stimmen_count' => function ($query) {
            $query->whereHas('terminAntworten');
        }])->latest()->get()->map(fn ($u) => [
            'id' => $u->id,
            'typ' => 'Terminumfrage',
            'titel' => $u->titel,
            'code' => $u->code,
            'erstellt_am' => $u->created_at,
            'ist_abgeschlossen' => $u->ist_abgeschlossen,
            'stimmen' => $u->moeglicheTermine->flatMap->terminAntworten->unique('id')->count(),
            'route_show' => route('terminumfrage.show', $u->code),
            'route_edit' => route('terminumfrage.edit', $u->id),
            'route_close' => route('terminumfrage.close', $u->id),
            'route_destroy' => route('terminumfrage.destroy', $u->id),
        ]);

        $textoptionumfragen = Textoptionenumfrage::latest()->get()->map(fn ($u) => [
            'id' => $u->id,
            'typ' => 'Textoptionumfrage',
            'titel' => $u->titel,
            'code' => $u->code,
            'erstellt_am' => $u->created_at,
            'ist_abgeschlossen' => $u->ist_abgeschlossen,
            'stimmen' => $u->textoptionen->flatMap->antworten->unique('teilnehmer')->count(),
            'route_show' => route('textoptionumfrage.show', $u->code),
            'route_edit' => route('textoptionumfrage.edit', $u->id),
            'route_close' => route('textoptionumfrage.close', $u->id),
            'route_destroy' => route('textoptionumfrage.destroy', $u->id),
        ]);

        $umfragen = $terminumfragen->concat($textoptionumfragen)->sortByDesc('erstellt_am')->values();

        return view('dashboard.index', compact('umfragen'));
    }

    public function closeTerminumfrage(Terminumfrage $terminumfrage)
    {
        $terminumfrage->update(['ist_abgeschlossen' => true]);

        return redirect()->route('dashboard')->with('success', 'Terminumfrage abgeschlossen.');
    }

    public function destroyTerminumfrage(Terminumfrage $terminumfrage)
    {
        $terminumfrage->delete();

        return redirect()->route('dashboard')->with('success', 'Terminumfrage gelöscht.');
    }

    public function closeTextoptionumfrage(Textoptionenumfrage $textoptionenumfrage)
    {
        $textoptionenumfrage->update(['ist_abgeschlossen' => true]);

        return redirect()->route('dashboard')->with('success', 'Textoptionumfrage abgeschlossen.');
    }

    public function destroyTextoptionumfrage(Textoptionenumfrage $textoptionenumfrage)
    {
        $textoptionenumfrage->delete();

        return redirect()->route('dashboard')->with('success', 'Textoptionumfrage gelöscht.');
    }
}
