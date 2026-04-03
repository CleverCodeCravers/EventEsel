@extends('layouts.app')

@section('title', 'Textoptionumfrage: ' . $umfrage->titel)
@section('heading', 'Textoptionumfrage')

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
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Option</th>
                    @unless($umfrage->ist_abgeschlossen)
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Auswahl</th>
                    @endunless
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stimmen</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Teilnehmer</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($optionen as $option)
                @php
                    $votes = 0;
                    $voters = [];
                    foreach ($antworten as $teilnehmer => $optionIds) {
                        if (in_array($option->id, $optionIds)) {
                            $votes++;
                            $voters[] = $teilnehmer;
                        }
                    }
                @endphp
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $option->text }}</td>
                    @unless($umfrage->ist_abgeschlossen)
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        <input type="checkbox" name="optionen[]" value="{{ $option->id }}" form="vote-form" class="h-4 w-4 text-indigo-600 border-gray-300 rounded">
                    </td>
                    @endunless
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $votes }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ implode(', ', $voters) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Abstimmungsformular (nur wenn nicht abgeschlossen) --}}
    @unless($umfrage->ist_abgeschlossen)
    <form id="vote-form" method="POST" action="{{ route('textoptionumfrage.vote', $umfrage->code) }}" class="space-y-6">
        @csrf
        <div>
            <label for="teilnehmer" class="block text-sm font-medium text-gray-700">Dein Name:</label>
            <input type="text" id="teilnehmer" name="teilnehmer" required value="{{ old('teilnehmer') }}"
                class="mt-1 block w-1/2 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
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
