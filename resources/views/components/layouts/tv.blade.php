<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">


<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TV Informasi Nagari Sikucur</title>
  <link rel="shortcut icon" href="{{ asset('logo/logo.png') }}">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/fastbootstrap@2.2.0/dist/css/fastbootstrap.min.css" rel="stylesheet"
    integrity="sha256-V6lu+OdYNKTKTsVFBuQsyIlDiRWiOmtC8VQ8Lzdm2i4=" crossorigin="anonymous">
  <style>
    html,
    body {
      height: 100%;
    }

    .card-body iframe {
      min-height: 100%;
      border: none;
      /* Hapus border default jika ada */
    }
  </style>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
  <style>
    #absensi-container {
      height: 65vh;
      /* Tinggi maksimal daftar */
      overflow: hidden;
      /* Sembunyikan elemen keluar */
      position: relative;
      /* Agar transform tidak menggeser layout */
    }

    #absensi-container ul {
      will-change: transform;
      transition: transform 0.5s linear;
    }
  </style>
  @livewireStyles

</head>

<body class="d-flex flex-column">


  {{ $slot }}

  <!-- <footer class="bg-dark text-white py-3">
              <marquee width="100%">This is a sample scrolling text.</marquee>
          </footer> -->
  @livewireScripts
  @vite('resources/js/supabase.js')

  <!-- Load Bootstrap -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  @stack('scripts')
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const container = document.querySelector("#absensi-container ul");
      let scrollY = 0;
      let direction = 1;

      setInterval(() => {
        if (!container) return;

        scrollY += direction * 1; // kecepatan
        container.style.transform = `translateY(-${scrollY}px)`;

        // reset saat scroll habis
        if (scrollY >= container.scrollHeight - container.parentElement.clientHeight) {
          direction = -1;
        } else if (scrollY <= 0) {
          direction = 1;
        }
      }, 50); // interval kecepatan
    });
  </script>

</body>

</html>
