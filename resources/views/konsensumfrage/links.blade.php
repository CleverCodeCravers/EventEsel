@extends('layouts.app')

@section('title', 'Konsensumfrage: ' . $konsensumfrage->titel)
@section('heading', $konsensumfrage->titel)

@section('subheading')
    <p class="text-sm text-gray-500 mt-1">{{ $abgestimmt }} von {{ $totalTeilnehmer }} haben abgestimmt</p>
@endsection

@section('content')
{{-- Ergebnisse --}}
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h2 class="text-lg font-bold mb-4">Ergebnisse</h2>
    @if($abgestimmt === 0)
        <p class="text-gray-500">Noch keine Stimmen abgegeben.</p>
    @else
        <div class="space-y-3">
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
    @endif
</div>

{{-- Teilnehmer-Links --}}
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <h2 class="text-lg font-bold mb-4">Teilnehmer-Links</h2>
    <p class="mb-4 text-sm text-gray-600">Verteile die persönlichen Links an die Teilnehmer. Jeder Link kann nur einmal zur Abstimmung verwendet werden.</p>

    <div class="space-y-3">
        @foreach($teilnehmer as $t)
        <div class="flex items-center space-x-3 p-3 rounded border {{ $t->hat_abgestimmt ? 'bg-green-50 border-green-200' : 'bg-white' }}">
            <span class="font-medium w-32">{{ $t->name }}</span>
            @if($t->hat_abgestimmt)
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    hat abgestimmt
                </span>
            @else
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">ausstehend</span>
            @endif
            <input type="text" readonly value="{{ route('konsensumfrage.show', [$konsensumfrage->code, $t->code]) }}"
                class="flex-1 text-sm border-gray-300 rounded bg-gray-50">
            <button type="button" onclick="copyLink(this, '{{ route('konsensumfrage.show', [$konsensumfrage->code, $t->code]) }}')"
                class="text-gray-500 hover:text-gray-800 px-2" title="Link kopieren">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            </button>
        </div>
        @endforeach
    </div>
</div>

<div class="flex space-x-4">
    <a href="{{ route('konsensumfrage.edit', $konsensumfrage) }}"
        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
        Bearbeiten
    </a>
    <a href="{{ route('dashboard') }}"
        class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50">
        Zurück zum Dashboard
    </a>
</div>

<script>
function copyLink(btn, url) {
    navigator.clipboard.writeText(url).then(() => {
        const orig = btn.innerHTML;
        btn.innerHTML = '<svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
        setTimeout(() => btn.innerHTML = orig, 1500);
    });
}
</script>
@endsection
