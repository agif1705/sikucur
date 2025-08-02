<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="retro">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5" rel="stylesheet" type="text/css" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link href="https://cdn.jsdelivr.net/npm/daisyui@5/themes.css" rel="stylesheet" type="text/css" />
    <title>{{ $title ?? 'Nagari Sikucur' }}</title>
    <link rel="shortcut icon" href="{{ asset('logo/logo.png') }}">

</head>
@livewireStyles

<body>
    <x-landing.navigation />
    {{ $slot }}
    @livewireScripts
</body>

</html>
