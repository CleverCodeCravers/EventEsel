@extends('layouts.app')

@section('title', 'Terminumfrage: ' . $umfrage->titel)
@section('heading', 'Terminumfrage')

@section('content')
<div class="border-4 border-dashed border-gray-200 rounded-lg p-4">
    <h2 class="text-2xl font-bold mb-4">{{ $umfrage->titel }}</h2>
    @if($umfrage->beschreibung)
        <p class="mb-4">{!! nl2br(e($umfrage->beschreibung)) !!}</p>
    @endif

    @if($umfrage->ist_abgeschlossen)
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded mb-4">
            Diese Umfrage ist abgeschlossen. Es kann nicht mehr abgestimmt werden.
        </div>
    @endif

    {{-- Ergebnis-Tabelle (immer sichtbar) --}}
    <div class="overflow-x-auto mb-6">
        <table class="w-full mb-4 bg-white border border-gray-500 rounded-lg">
            <thead>
                <tr>
                    <th class="text-left px-4 py-2 border-b border-gray-200 whitespace-nowrap">Datum</th>
                    @unless($umfrage->ist_abgeschlossen)
                    <th class="text-left px-4 py-2 border-b border-gray-200 whitespace-nowrap">Deine Stimme</th>
                    @endunless
                    <th class="text-left px-4 py-2 border-b border-gray-200 whitespace-nowrap">Stimmen bisher</th>
                    <th class="text-left px-4 py-2 border-b border-gray-200">von</th>
                </tr>
            </thead>
            <tbody>
                @foreach($termine as $termin)
                @php
                    $count = 0;
                    $voters = [];
                    foreach ($antworten as $teilnehmer => $termine_ids) {
                        if (isset($termine_ids[$termin->id])) {
                            $count++;
                            $voters[] = $teilnehmer;
                        }
                    }
                @endphp
                <tr class="border-b border-gray-500">
                    <td class="px-4 py-2 border-b border-gray-200">{{ $termin->datum->format('d.m.Y') }}</td>
                    @unless($umfrage->ist_abgeschlossen)
                    <td class="px-4 py-2 border-b border-gray-200">
                        <input type="checkbox" name="termine[]" value="{{ $termin->id }}" form="vote-form" class="mr-2">
                    </td>
                    @endunless
                    <td class="px-4 py-2 border-b border-gray-200">{{ $count }}</td>
                    <td class="px-4 py-2 border-b border-gray-200">{{ implode(', ', $voters) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Abstimmungsformular (nur wenn nicht abgeschlossen) --}}
    @unless($umfrage->ist_abgeschlossen)
    <form id="vote-form" method="POST" action="{{ route('terminumfrage.vote', $umfrage->code) }}" class="space-y-6">
        @csrf
        <div>
            <label for="teilnehmer" class="block text-sm font-medium text-gray-700">Dein Name:</label>
            <input type="text" id="teilnehmer" name="teilnehmer" required value="{{ old('teilnehmer') }}"
                class="mt-1 block w-full sm:w-1/2 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        </div>
        <div>
            <button type="submit"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                Abstimmen
            </button>
        </div>
    </form>
    @endunless
</div>
@endsection
