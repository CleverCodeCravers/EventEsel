@extends('layouts.app')

@section('title', 'Meine Umfragen')
@section('heading', 'Meine Umfragen')

@section('content')
<div class="mb-4 flex space-x-4">
    <a href="{{ route('terminumfrage.create') }}"
        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
        + Terminumfrage
    </a>
    <a href="{{ route('textoptionumfrage.create') }}"
        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
        + Textoptionumfrage
    </a>
</div>

@if($umfragen->isEmpty())
    <p class="text-gray-500">Noch keine Umfragen vorhanden.</p>
@else
<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 bg-white shadow rounded-lg">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Typ</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Titel</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Erstellt am</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stimmen</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Link</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aktionen</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach($umfragen as $umfrage)
            <tr class="{{ $umfrage['ist_abgeschlossen'] ? 'bg-gray-100 text-gray-400' : '' }}">
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $umfrage['typ'] === 'Terminumfrage' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                        {{ $umfrage['typ'] }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <a href="{{ $umfrage['route_show'] }}" class="{{ $umfrage['ist_abgeschlossen'] ? 'text-gray-400' : 'text-indigo-600 hover:underline' }}">
                        {{ $umfrage['titel'] }}
                    </a>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $umfrage['erstellt_am']->format('d.m.Y H:i') }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    @if($umfrage['ist_abgeschlossen'])
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-600">Abgeschlossen</span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktiv</span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $umfrage['stimmen'] }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                    <a href="{{ $umfrage['route_show'] }}" target="_blank" class="text-indigo-600 hover:text-indigo-800" title="Umfrage öffnen">
                        <svg class="inline w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                    <button type="button" onclick="copyLink('{{ $umfrage['route_show'] }}')" class="text-gray-500 hover:text-gray-800" title="Link kopieren">
                        <svg class="inline w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                    <a href="{{ $umfrage['route_edit'] }}" class="text-indigo-600 hover:text-indigo-800 font-medium" title="Bearbeiten">
                        <svg class="inline w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </a>
                    @if(!$umfrage['ist_abgeschlossen'])
                    <form method="POST" action="{{ $umfrage['route_close'] }}" class="inline"
                        onsubmit="return confirm('Möchten Sie diese Umfrage wirklich abschließen?')">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="text-yellow-600 hover:text-yellow-800 font-medium">Abschließen</button>
                    </form>
                    @endif
                    <form method="POST" action="{{ $umfrage['route_destroy'] }}" class="inline"
                        onsubmit="return confirm('Möchten Sie diese Umfrage wirklich löschen? Alle Stimmen gehen verloren.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Löschen</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<script>
function copyLink(url) {
    navigator.clipboard.writeText(url).then(() => {
        const btn = event.currentTarget;
        const orig = btn.innerHTML;
        btn.innerHTML = '<svg class="inline w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
        setTimeout(() => btn.innerHTML = orig, 1500);
    });
}
</script>
@endsection
