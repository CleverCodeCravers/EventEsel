@extends('layouts.app')

@section('title', 'Neue Terminumfrage erstellen')
@section('heading', 'Neue Terminumfrage erstellen')

@section('subheading')
    @if(session('teilnahme_link'))
        <p class="mt-2">Teilnahmelink: <a href="{{ session('teilnahme_link') }}" class="text-blue-600 hover:underline">{{ session('teilnahme_link') }}</a></p>
    @endif
@endsection

@section('content')
<div class="border-4 border-dashed border-gray-200 rounded-lg p-4">
    <form method="POST" action="{{ route('terminumfrage.store') }}" class="space-y-6">
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
            <label class="block text-sm font-medium text-gray-700">Mögliche Termine:</label>
            <div id="termine-container" class="space-y-2">
                <div class="flex items-center space-x-2 mb-2">
                    <input type="date" name="termine[]" required
                        class="block w-full sm:w-1/3 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                    <button type="button" class="text-red-600 hover:text-red-800" onclick="this.parentElement.remove()">&#10005;</button>
                </div>
            </div>
            <button type="button" onclick="addTerminField()"
                class="mt-2 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                Weiteren Termin hinzufügen
            </button>
        </div>

        <div class="mt-6">
            <h3 class="text-lg font-medium text-gray-900">Terminmuster-Generator (optional)</h3>
            <div class="mt-2 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Wochentage auswählen:</label>
                    <div class="mt-2 space-x-2">
                        @foreach(['Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa', 'So'] as $day)
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="days[]" value="{{ $day }}" class="rounded border-gray-300 text-indigo-600">
                            <span class="ml-2">{{ $day }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                <div class="flex space-x-4">
                    <div>
                        <label for="start-date" class="block text-sm font-medium text-gray-700">Startdatum:</label>
                        <input type="date" id="start-date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                    <div>
                        <label for="end-date" class="block text-sm font-medium text-gray-700">Enddatum:</label>
                        <input type="date" id="end-date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                    </div>
                </div>
                <button type="button" onclick="generateDates()"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                    Termine hinzufügen
                </button>
            </div>
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
function addTerminField() {
    const container = document.getElementById("termine-container");
    const wrapper = document.createElement("div");
    wrapper.className = "flex items-center space-x-2 mb-2";
    wrapper.innerHTML = `
        <input type="date" name="termine[]" required class="block w-full sm:w-1/3 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
        <button type="button" class="text-red-600 hover:text-red-800" onclick="this.parentElement.remove()">&#10005;</button>
    `;
    container.appendChild(wrapper);
}

function generateDates() {
    const startDate = document.getElementById('start-date').value;
    const endDate = document.getElementById('end-date').value;
    const selectedDays = Array.from(document.querySelectorAll('input[name="days[]"]:checked')).map(cb => cb.value);
    const dayMap = {'So': 0, 'Mo': 1, 'Di': 2, 'Mi': 3, 'Do': 4, 'Fr': 5, 'Sa': 6};

    if (!startDate || !endDate || selectedDays.length === 0) {
        alert('Bitte Start-/Enddatum und mindestens einen Wochentag auswählen.');
        return;
    }

    let current = new Date(startDate);
    const end = new Date(endDate);
    const existingFields = document.querySelectorAll('#termine-container input[type="date"]');
    const existingDates = new Set(Array.from(existingFields).map(f => f.value));

    while (current <= end) {
        const dayName = ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'][current.getDay()];
        if (selectedDays.includes(dayName)) {
            const dateStr = current.toISOString().split('T')[0];
            if (!existingDates.has(dateStr)) {
                addTerminField();
                const newFields = document.querySelectorAll('#termine-container input[type="date"]');
                newFields[newFields.length - 1].value = dateStr;
                existingDates.add(dateStr);
            }
        }
        current.setDate(current.getDate() + 1);
    }
}
</script>
@endsection
