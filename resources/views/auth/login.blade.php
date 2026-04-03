@extends('layouts.app')

@section('title', 'Admin Login')
@section('heading', 'Admin Login')

@section('content')
<div class="flex items-center justify-center px-4">
    <div class="bg-white p-6 rounded shadow-md w-full max-w-sm">
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700">Benutzername:</label>
                <input type="text" id="username" name="username" value="{{ old('username') }}" required autofocus
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Passwort:</label>
                <input type="password" id="password" name="password" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
            </div>
            <button type="submit"
                class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Anmelden
            </button>
        </form>
    </div>
</div>
@endsection
