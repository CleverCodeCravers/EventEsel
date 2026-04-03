@extends('layouts.app')

@section('title', 'Neue Textoptionumfrage erstellen')
@section('heading', 'Neue Textoptionumfrage erstellen')

@section('subheading')
    @if(session('teilnahme_link'))
        <p class="mt-2">Teilnahmelink: <a href="{{ session('teilnahme_link') }}" class="text-blue-600 hover:underline">{{ session('teilnahme_link') }}</a></p>
    @endif
@endsection

@section('content')
<div class="border-4 border-dashed border-gray-200 rounded-lg p-4">
    <form method="POST" action="{{ route('textoptionumfrage.store') }}" class="space-y-6">
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
            <label class="block text-sm font-medium text-gray-700">Textoptionen:</label>
            <div id="optionen-container" class="space-y-2">
                <div class="flex items-center space-x-2 mb-2">
                    <input type="text" name="optionen[]" required
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <button type="button" class="text-red-600 hover:text-red-800" onclick="this.parentElement.remove()">&#10005;</button>
                </div>
            </div>
            <button type="button" onclick="addOptionField()"
                class="mt-2 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                Weitere Option hinzufügen
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
function addOptionField() {
    const container = document.getElementById("optionen-container");
    const wrapper = document.createElement("div");
    wrapper.className = "flex items-center space-x-2 mb-2";
    wrapper.innerHTML = `
        <input type="text" name="optionen[]" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        <button type="button" class="text-red-600 hover:text-red-800" onclick="this.parentElement.remove()">&#10005;</button>
    `;
    container.appendChild(wrapper);
}
</script>
@endsection
