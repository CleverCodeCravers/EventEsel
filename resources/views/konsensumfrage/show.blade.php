@extends('layouts.app')

@section('title', 'Konsensumfrage: ' . $umfrage->titel)
@section('heading', 'Konsensumfrage')

@section('content')
<div class="border-4 border-dashed border-gray-200 rounded-lg p-4">
    <h2 class="text-2xl font-bold mb-2">{{ $umfrage->titel }}</h2>
    @if($umfrage->beschreibung)
        <p class="mb-4 text-gray-600">{!! nl2br(e($umfrage->beschreibung)) !!}</p>
    @endif

    <p class="mb-4 text-sm text-gray-500">
        Hallo <strong>{{ $teilnehmer->name }}</strong> — {{ $abgestimmt }} von {{ $totalTeilnehmer }} haben abgestimmt.
    </p>

    @if($umfrage->ist_abgeschlossen)
        <div class="bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded mb-4">
            Diese Umfrage ist abgeschlossen.
        </div>
    @endif

    @if($teilnehmer->hat_abgestimmt)
        <div class="bg-blue-100 border border-blue-400 text-blue-800 px-4 py-3 rounded mb-4">
            Du hast bereits abgestimmt.
        </div>
    @endif

    {{-- Ergebnisse (immer sichtbar) --}}
    <div class="space-y-3 mb-6">
        @foreach($ergebnisse as $ergebnis)
        <div>
            <div class="flex justify-between text-sm font-medium mb-1">
                <span>{{ $ergebnis['text'] }}</span>
                <span>{{ $ergebnis['stimmen'] }} Stimmen ({{ $ergebnis['prozent'] }}%)</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-4">
                <div class="bg-indigo-600 h-4 rounded-full transition-all" style="width: {{ $ergebnis['prozent'] }}%"></div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Abstimmungsformular --}}
    @if(!$teilnehmer->hat_abgestimmt && !$umfrage->ist_abgeschlossen)
    <form method="POST" action="{{ route('konsensumfrage.vote', [$umfrage->code, $teilnehmer->code]) }}" class="space-y-4">
        @csrf
        <h3 class="text-lg font-bold">Deine Wahl:</h3>
        <div class="space-y-2">
            @foreach($optionen as $option)
            <label class="flex items-center space-x-3 p-3 bg-white border rounded hover:bg-gray-50 cursor-pointer">
                <input type="radio" name="option" value="{{ $option->id }}" required
                    class="h-4 w-4 text-indigo-600 border-gray-300">
                <span>{{ $option->text }}</span>
            </label>
            @endforeach
        </div>
        <button type="submit"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
            Stimme abgeben
        </button>
    </form>
    @endif
</div>
@endsection
