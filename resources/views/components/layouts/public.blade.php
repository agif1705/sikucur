<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="application-name" content="{{ config('app.name') }}">
  <link rel="shortcut icon" href="{{ asset('logo/logo.png') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ config('app.name') }}</title>
  <title>Form Izin Pegawai</title>
  {{-- Load CSS Filament + Tailwind --}}
  @vite(['resources/css/app.css', 'resources/js/app.js', 'resources/css/filament/admin/theme.css'])
  @filamentStyles
</head>

<body class="bg-black dark:bg-indigo-950 min-h-screen flex items-center justify-center">


  <div class="w-full max-w-lg bg-gray-900 dark:bg-gray-800 shadow-lg rounded-lg p-8">
    {{ $slot }}
  </div>

  {{-- Load JS Filament --}}
  @filamentScripts
</body>

</html>
