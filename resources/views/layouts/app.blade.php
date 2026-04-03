<!DOCTYPE html>
<html lang="de" class="h-full bg-gray-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'EventEsel')</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="h-full">
    <div class="min-h-full">
        @auth
        <nav class="bg-indigo-600" x-data="{ open: false }">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-14">
                    <a href="{{ route('dashboard') }}" class="text-white text-xl font-bold">EventEsel</a>

                    {{-- Desktop Nav --}}
                    <ul class="hidden md:flex space-x-4 items-center text-sm">
                        <li><a href="{{ route('dashboard') }}" class="text-white hover:text-indigo-200">Dashboard</a></li>
                        <li><a href="{{ route('terminumfrage.create') }}" class="text-white hover:text-indigo-200">Terminumfrage</a></li>
                        <li><a href="{{ route('textoptionumfrage.create') }}" class="text-white hover:text-indigo-200">Textoptionumfrage</a></li>
                        <li><a href="{{ route('konsensumfrage.create') }}" class="text-white hover:text-indigo-200">Konsensumfrage</a></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" class="inline">
                                @csrf
                                <button type="submit" class="text-indigo-200 hover:text-white">Abmelden</button>
                            </form>
                        </li>
                    </ul>

                    {{-- Hamburger --}}
                    <button onclick="document.getElementById('mobile-menu').classList.toggle('hidden')" class="md:hidden text-white p-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>

            {{-- Mobile Nav --}}
            <div id="mobile-menu" class="hidden md:hidden border-t border-indigo-500">
                <div class="px-4 py-3 space-y-2">
                    <a href="{{ route('dashboard') }}" class="block text-white hover:bg-indigo-500 rounded px-3 py-2">Dashboard</a>
                    <a href="{{ route('terminumfrage.create') }}" class="block text-white hover:bg-indigo-500 rounded px-3 py-2">Terminumfrage</a>
                    <a href="{{ route('textoptionumfrage.create') }}" class="block text-white hover:bg-indigo-500 rounded px-3 py-2">Textoptionumfrage</a>
                    <a href="{{ route('konsensumfrage.create') }}" class="block text-white hover:bg-indigo-500 rounded px-3 py-2">Konsensumfrage</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-left text-indigo-200 hover:bg-indigo-500 hover:text-white rounded px-3 py-2">Abmelden</button>
                    </form>
                </div>
            </div>
        </nav>
        @endauth

        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-4 px-4 sm:py-6 sm:px-6 lg:px-8">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">@yield('heading')</h1>
                @yield('subheading')
            </div>
        </header>

        <main>
            <div class="max-w-7xl mx-auto py-4 px-4 sm:py-6 sm:px-6 lg:px-8">
                @if(session('success'))
                    <div class="bg-green-500 text-white font-bold text-center p-3 sm:p-4 mb-4 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        @foreach($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
