@extends('layouts.app')

@section('title', 'Neue Konsensumfrage erstellen')
@section('heading', 'Neue Konsensumfrage erstellen')

@section('content')
<div class="border-4 border-dashed border-gray-200 rounded-lg p-4">
    <form method="POST" action="{{ route('konsensumfrage.store') }}" class="space-y-6">
        @csrf
        <div>
            <label for="titel" class="block text-sm font-medium text-gray-700">Titel:</label>
            <input type="text" id="titel" name="titel" maxlength="200" required value="{{ old('titel') }}"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        </div>
        <div>
            <label for="beschreibung" class="block text-sm font-medium text-gray-700">Beschreibung:</label>
            <textarea id="beschreibung" name="beschreibung" rows="4"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('beschreibung') }}</textarea>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Optionen (min. 2):</label>
            <div id="optionen-container" class="space-y-2">
                <div class="flex items-center space-x-2 mb-2">
                    <input type="text" name="optionen[]" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <button type="button" class="text-red-600 hover:text-red-800" onclick="this.parentElement.remove()">&#10005;</button>
                </div>
                <div class="flex items-center space-x-2 mb-2">
                    <input type="text" name="optionen[]" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <button type="button" class="text-red-600 hover:text-red-800" onclick="this.parentElement.remove()">&#10005;</button>
                </div>
            </div>
            <button type="button" onclick="addField('optionen-container', 'optionen[]', 'text')"
                class="mt-2 inline-flex items-center px-3 py-1 border text-sm rounded-md text-indigo-600 border-indigo-600 hover:bg-indigo-50">
                + Option
            </button>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Teilnehmer (min. 2):</label>
            <div id="teilnehmer-container" class="space-y-2">
                <div class="flex items-center space-x-2 mb-2">
                    <input type="text" name="teilnehmer[]" required placeholder="Name" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <button type="button" class="text-red-600 hover:text-red-800" onclick="this.parentElement.remove()">&#10005;</button>
                </div>
                <div class="flex items-center space-x-2 mb-2">
                    <input type="text" name="teilnehmer[]" required placeholder="Name" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <button type="button" class="text-red-600 hover:text-red-800" onclick="this.parentElement.remove()">&#10005;</button>
                </div>
            </div>
            <button type="button" onclick="addField('teilnehmer-container', 'teilnehmer[]', 'text', 'Name')"
                class="mt-2 inline-flex items-center px-3 py-1 border text-sm rounded-md text-indigo-600 border-indigo-600 hover:bg-indigo-50">
                + Teilnehmer
            </button>
        </div>

        <div>
            <button type="submit"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                Erstellen
            </button>
        </div>
    </form>
</div>

<script>
function addField(containerId, name, type, placeholder) {
    const container = document.getElementById(containerId);
    const wrapper = document.createElement("div");
    wrapper.className = "flex items-center space-x-2 mb-2";
    const input = document.createElement("input");
    input.type = type;
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
