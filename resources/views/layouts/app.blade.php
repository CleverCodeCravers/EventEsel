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
        <nav class="bg-indigo-600 p-4">
            <div class="max-w-7xl mx-auto flex justify-between items-center">
                <a href="{{ route('dashboard') }}" class="text-white text-2xl font-bold">EventEsel</a>
                <ul class="flex space-x-4 items-center">
                    <li><a href="{{ route('dashboard') }}" class="text-white hover:underline">Dashboard</a></li>
                    <li><a href="{{ route('terminumfrage.create') }}" class="text-white hover:underline">Terminumfrage</a></li>
                    <li><a href="{{ route('textoptionumfrage.create') }}" class="text-white hover:underline">Textoptionumfrage</a></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-white hover:underline">Abmelden</button>
                        </form>
                    </li>
                </ul>
            </div>
        </nav>
        @endauth

        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h1 class="text-3xl font-bold text-gray-900">@yield('heading')</h1>
                @yield('subheading')
            </div>
        </header>

        <main>
            <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
                <div class="px-4 py-6 sm:px-0">
                    @if(session('success'))
                        <div class="bg-green-500 text-white font-bold text-lg text-center p-4 mb-4 rounded">
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
            </div>
        </main>
    </div>
</body>
</html>
