<!doctype html>
<html lang="en">
<head>
    @vite(['resources/js/app.js', 'resources/css/app.css'])
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Playlits - @yield('title')</title>
    <style>
        body {
            background: #FFF;
        }
    </style>
</head>
<body>
<div class="max-w-lg mx-auto py-2">
    <header class="mb-8">
        <div class="px-4 sm:px-0">
            <h1 class="text-xl font-bold">playlits.</h1>
        </div>
    </header>
    @yield('content')
    <p>Happy listening!</p>
    <p class="font-bold">— playlits.</p>
</div>
</body>
</html>
