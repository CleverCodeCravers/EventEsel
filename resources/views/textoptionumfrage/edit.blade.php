@extends('layouts.app')

@section('title', 'Textoptionumfrage bearbeiten')
@section('heading', 'Textoptionumfrage bearbeiten')

@section('content')
<div class="border-4 border-dashed border-gray-200 rounded-lg p-4">
    <form method="POST" action="{{ route('textoptionumfrage.update', $textoptionenumfrage) }}" class="space-y-6">
        @csrf
        @method('PUT')
        <div>
            <label for="titel" class="block text-sm font-medium text-gray-700">Titel:</label>
            <input type="text" id="titel" name="titel" maxlength="200" required
                value="{{ old('titel', $textoptionenumfrage->titel) }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        </div>
        <div>
            <label for="beschreibung" class="block text-sm font-medium text-gray-700">Beschreibung:</label>
            <textarea id="beschreibung" name="beschreibung" rows="4"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('beschreibung', $textoptionenumfrage->beschreibung) }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Bestehende Optionen:</label>
            <div id="bestehende-container" class="space-y-2">
                @foreach($optionen as $option)
                <div class="flex items-center space-x-2 mb-2">
                    <input type="checkbox" name="bestehende_optionen[]" value="{{ $option->id }}" checked
                        class="rounded border-gray-300 text-indigo-600">
                    <span class="text-sm">{{ $option->text }}</span>
                    <span class="text-xs text-gray-400">(Abwählen = entfernen inkl. Stimmen)</span>
                </div>
                @endforeach
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Neue Optionen hinzufügen:</label>
            <div id="neue-optionen-container" class="space-y-2"></div>
            <button type="button" onclick="addNewOption()"
                class="mt-2 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                Option hinzufügen
            </button>
        </div>

        <div class="flex space-x-4">
            <button type="submit"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                Speichern
            </button>
            <a href="{{ route('dashboard') }}"
                class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50">
                Abbrechen
            </a>
        </div>
    </form>
</div>

<script>
function addNewOption() {
    const container = document.getElementById("neue-optionen-container");
    const wrapper = document.createElement("div");
    wrapper.className = "flex items-center space-x-2 mb-2";
    wrapper.innerHTML = `
        <input type="text" name="neue_optionen[]" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        <button type="button" class="text-red-600 hover:text-red-800" onclick="this.parentElement.remove()">&#10005;</button>
    `;
    container.appendChild(wrapper);
}
</script>
@endsection
