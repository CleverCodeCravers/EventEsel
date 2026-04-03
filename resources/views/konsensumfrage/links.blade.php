@extends('layouts.app')

@section('title', 'Teilnehmer-Links')
@section('heading', 'Teilnehmer-Links: ' . $konsensumfrage->titel)

@section('content')
<div class="border-4 border-dashed border-gray-200 rounded-lg p-4">
    <p class="mb-4 text-gray-600">Verteile diese persönlichen Links an die Teilnehmer. Jeder Link kann nur einmal zur Abstimmung verwendet werden.</p>

    <div class="space-y-3">
        @foreach($teilnehmer as $t)
        <div class="flex items-center space-x-3 bg-white p-3 rounded border">
            <span class="font-medium w-32">{{ $t->name }}</span>
            <input type="text" readonly value="{{ route('konsensumfrage.show', [$konsensumfrage->code, $t->code]) }}"
                class="flex-1 text-sm border-gray-300 rounded bg-gray-50">
            <button type="button" onclick="copyLink(this, '{{ route('konsensumfrage.show', [$konsensumfrage->code, $t->code]) }}')"
                class="text-gray-500 hover:text-gray-800 px-2" title="Link kopieren">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            </button>
        </div>
        @endforeach
    </div>

    <div class="mt-6">
        <a href="{{ route('dashboard') }}"
            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
            Zurück zum Dashboard
        </a>
    </div>
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
