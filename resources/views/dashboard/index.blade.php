@extends('layouts.app')

@section('title', 'Meine Umfragen')
@section('heading', 'Meine Umfragen')

@section('content')
<div class="mb-4 flex flex-wrap gap-2">
    <a href="{{ route('terminumfrage.create') }}"
        class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
        + Terminumfrage
    </a>
    <a href="{{ route('textoptionumfrage.create') }}"
        class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
        + Textoptionumfrage
    </a>
    <a href="{{ route('konsensumfrage.create') }}"
        class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
        + Konsensumfrage
    </a>
</div>

@if($umfragen->isEmpty())
    <p class="text-gray-500">Noch keine Umfragen vorhanden.</p>
@else

{{-- Mobile: Card-Layout --}}
<div class="space-y-3 md:hidden">
    @foreach($umfragen as $umfrage)
    <div class="bg-white rounded-lg shadow p-4 {{ $umfrage['ist_abgeschlossen'] ? 'opacity-60' : '' }}">
        <div class="flex items-start justify-between mb-2">
            <div class="flex-1 min-w-0">
                <a href="{{ $umfrage['route_show'] }}" class="text-sm font-semibold {{ $umfrage['ist_abgeschlossen'] ? 'text-gray-400' : 'text-indigo-600' }} truncate block">
                    {{ $umfrage['titel'] }}
                </a>
                <div class="flex items-center gap-2 mt-1">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $umfrage['typ'] === 'Terminumfrage' ? 'bg-blue-100 text-blue-800' : ($umfrage['typ'] === 'Konsensumfrage' ? 'bg-amber-100 text-amber-800' : 'bg-purple-100 text-purple-800') }}">
                        {{ $umfrage['typ'] }}
                    </span>
                    @if($umfrage['ist_abgeschlossen'])
                        <span class="text-xs text-gray-400">Abgeschlossen</span>
                    @else
                        <span class="text-xs text-green-600">Aktiv</span>
                    @endif
                </div>
            </div>
            <span class="text-xs text-gray-400 ml-2 whitespace-nowrap">{{ $umfrage['stimmen'] }} Stimmen</span>
        </div>
        <div class="text-xs text-gray-400 mb-3">{{ $umfrage['erstellt_am']->format('d.m.Y H:i') }}</div>
        <div class="flex items-center gap-3 border-t pt-3">
            <a href="{{ $umfrage['route_edit'] }}" class="text-indigo-600 text-sm">Bearbeiten</a>
            <a href="{{ $umfrage['route_show'] }}" target="_blank" class="text-indigo-600 text-sm">Öffnen</a>
            <button type="button" onclick="copyLink(this, '{{ $umfrage['route_show'] }}')" class="text-gray-500 text-sm">Link kopieren</button>
            @if(!$umfrage['ist_abgeschlossen'])
            <form method="POST" action="{{ $umfrage['route_close'] }}" class="inline" onsubmit="return confirm('Umfrage wirklich abschließen?')">
                @csrf @method('PATCH')
                <button type="submit" class="text-yellow-600 text-sm">Abschließen</button>
            </form>
            @endif
            <form method="POST" action="{{ $umfrage['route_destroy'] }}" class="inline ml-auto" onsubmit="return confirm('Umfrage wirklich löschen? Alle Stimmen gehen verloren.')">
                @csrf @method('DELETE')
                <button type="submit" class="text-red-600 text-sm">Löschen</button>
            </form>
        </div>
    </div>
    @endforeach
</div>

{{-- Desktop: Tabelle --}}
<div class="hidden md:block overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200 bg-white shadow rounded-lg">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Typ</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Titel</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Erstellt</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stimmen</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Link</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aktionen</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach($umfragen as $umfrage)
            <tr class="{{ $umfrage['ist_abgeschlossen'] ? 'bg-gray-100 text-gray-400' : '' }}">
                <td class="px-4 py-3 whitespace-nowrap text-sm">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $umfrage['typ'] === 'Terminumfrage' ? 'bg-blue-100 text-blue-800' : ($umfrage['typ'] === 'Konsensumfrage' ? 'bg-amber-100 text-amber-800' : 'bg-purple-100 text-purple-800') }}">
                        {{ $umfrage['typ'] }}
                    </span>
                </td>
                <td class="px-4 py-3 text-sm font-medium max-w-[200px] truncate">
                    <a href="{{ $umfrage['route_show'] }}" class="{{ $umfrage['ist_abgeschlossen'] ? 'text-gray-400' : 'text-indigo-600 hover:underline' }}">
                        {{ $umfrage['titel'] }}
                    </a>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $umfrage['erstellt_am']->format('d.m.Y') }}</td>
                <td class="px-4 py-3 whitespace-nowrap text-sm">
                    @if($umfrage['ist_abgeschlossen'])
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-200 text-gray-600">Abgeschlossen</span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Aktiv</span>
                    @endif
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-sm">{{ $umfrage['stimmen'] }}</td>
                <td class="px-4 py-3 whitespace-nowrap text-sm space-x-1">
                    <a href="{{ $umfrage['route_show'] }}" target="_blank" class="text-indigo-600 hover:text-indigo-800" title="Öffnen">
                        <svg class="inline w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    </a>
                    <button type="button" onclick="copyLink(this, '{{ $umfrage['route_show'] }}')" class="text-gray-500 hover:text-gray-800" title="Link kopieren">
                        <svg class="inline w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    </button>
                </td>
                <td class="px-4 py-3 whitespace-nowrap text-sm space-x-1">
                    <a href="{{ $umfrage['route_edit'] }}" class="text-indigo-600 hover:text-indigo-800" title="Bearbeiten">
                        <svg class="inline w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </a>
                    @if(!$umfrage['ist_abgeschlossen'])
                    <form method="POST" action="{{ $umfrage['route_close'] }}" class="inline" onsubmit="return confirm('Umfrage wirklich abschließen?')">
                        @csrf @method('PATCH')
                        <button type="submit" class="text-yellow-600 hover:text-yellow-800" title="Abschließen">
                            <svg class="inline w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </button>
                    </form>
                    @endif
                    <form method="POST" action="{{ $umfrage['route_destroy'] }}" class="inline" onsubmit="return confirm('Umfrage wirklich löschen?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-800" title="Löschen">
                            <svg class="inline w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<script>
function copyLink(btn, url) {
    navigator.clipboard.writeText(url).then(() => {
        const orig = btn.innerHTML;
        if (btn.tagName === 'BUTTON' && btn.querySelector('svg')) {
            btn.innerHTML = '<svg class="inline w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>';
        } else {
            const origText = btn.textContent;
            btn.textContent = 'Kopiert!';
            setTimeout(() => btn.textContent = origText, 1500);
            return;
        }
        setTimeout(() => btn.innerHTML = orig, 1500);
    });
}
</script>
@endsection
