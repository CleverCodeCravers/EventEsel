@extends('layouts.app')

@section('title', 'Konsensumfrage bearbeiten')
@section('heading', 'Konsensumfrage bearbeiten')

@section('content')
<div class="border-4 border-dashed border-gray-200 rounded-lg p-4">
    <form method="POST" action="{{ route('konsensumfrage.update', $konsensumfrage) }}" class="space-y-6">
        @csrf
        @method('PUT')
        <div>
            <label for="titel" class="block text-sm font-medium text-gray-700">Titel:</label>
            <input type="text" id="titel" name="titel" maxlength="200" required
                value="{{ old('titel', $konsensumfrage->titel) }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        </div>
        <div>
            <label for="beschreibung" class="block text-sm font-medium text-gray-700">Beschreibung:</label>
            <textarea id="beschreibung" name="beschreibung" rows="4"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('beschreibung', $konsensumfrage->beschreibung) }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Bestehende Optionen:</label>
            @foreach($optionen as $option)
            <div class="flex items-center space-x-2 mb-2">
                <input type="checkbox" name="bestehende_optionen[]" value="{{ $option->id }}" checked class="rounded border-gray-300 text-indigo-600">
                <span class="text-sm">{{ $option->text }}</span>
                <span class="text-xs text-gray-400">(Abwählen = entfernen)</span>
            </div>
            @endforeach
            <label class="block text-sm font-medium text-gray-700 mt-4 mb-2">Neue Optionen:</label>
            <div id="neue-optionen-container" class="space-y-2"></div>
            <button type="button" onclick="addField('neue-optionen-container', 'neue_optionen[]')"
                class="mt-2 inline-flex items-center px-3 py-1 border text-sm rounded-md text-indigo-600 border-indigo-600 hover:bg-indigo-50">
                + Option
            </button>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Bestehende Teilnehmer:</label>
            @foreach($teilnehmer as $t)
            <div class="flex items-center space-x-2 mb-2">
                <input type="checkbox" name="bestehende_teilnehmer[]" value="{{ $t->id }}" checked
                    class="rounded border-gray-300 text-indigo-600"
                    {{ $t->hat_abgestimmt ? 'disabled' : '' }}>
                @if($t->hat_abgestimmt)
                    <input type="hidden" name="bestehende_teilnehmer[]" value="{{ $t->id }}">
                @endif
                <span class="text-sm">{{ $t->name }}</span>
                @if($t->hat_abgestimmt)
                    <span class="text-xs text-green-600">(hat abgestimmt — kann nicht entfernt werden)</span>
                @else
                    <span class="text-xs text-gray-400">(Abwählen = entfernen)</span>
                @endif
            </div>
            @endforeach
            <label class="block text-sm font-medium text-gray-700 mt-4 mb-2">Neue Teilnehmer:</label>
            <div id="neue-teilnehmer-container" class="space-y-2"></div>
            <button type="button" onclick="addField('neue-teilnehmer-container', 'neue_teilnehmer[]', 'Name')"
                class="mt-2 inline-flex items-center px-3 py-1 border text-sm rounded-md text-indigo-600 border-indigo-600 hover:bg-indigo-50">
                + Teilnehmer
            </button>
        </div>

        <div class="flex space-x-4">
            <button type="submit"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                Speichern
            </button>
            <a href="{{ route('konsensumfrage.links', $konsensumfrage) }}"
                class="inline-flex items-center px-3 py-2 border text-sm rounded-md text-indigo-600 border-indigo-600 hover:bg-indigo-50">
                Links anzeigen
            </a>
            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50">
                Abbrechen
            </a>
        </div>
    </form>
</div>

<script>
function addField(containerId, name, placeholder) {
    const container = document.getElementById(containerId);
    const wrapper = document.createElement("div");
    wrapper.className = "flex items-center space-x-2 mb-2";
    const input = document.createElement("input");
    input.type = "text";
    input.name = name;
    input.required = true;
    if (placeholder) input.placeholder = placeholder;
    input.className = "block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50";
    wrapper.appendChild(input);
    const btn = document.createElement("button");
    btn.type = "button";
    btn.className = "text-red-600 hover:text-red-800";
    btn.innerHTML = "&#10005;";
    btn.onclick = () => wrapper.remove();
    wrapper.appendChild(btn);
    container.appendChild(wrapper);
}
</script>
@endsection
