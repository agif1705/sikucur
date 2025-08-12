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
  @vite('resources/js/supabase.js')
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
  <style>
    .swiper-slide img {
      width: 100%;
      height: 500px;
      object-fit: cover;
      border-radius: 8px;
    }

    .swiper-pagination-bullet-active {
      background-color: #0d6efd;
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

  @stack('scripts')
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
  <script>
    document.addEventListener('livewire:navigated', initScroll);
    document.addEventListener('livewire:load', initScroll);

    function initScroll() {
      if (window.autoScrollRunning) return; // jangan double start
      window.autoScrollRunning = true;

      const container = document.getElementById('absensi-container');
      const list = container.querySelector('ul');
      let scrollDirection = 1;
      let position = 0;
      const scrollSpeed = 0.2;
      const tolerance = 2;

      function autoScroll() {
        const maxScroll = list.scrollHeight - container.clientHeight;
        position += scrollSpeed * scrollDirection;

        if (position >= maxScroll - tolerance) {
          scrollDirection = -1;
        } else if (position <= 0) {
          scrollDirection = 1;
        }

        list.style.transform = `translate3d(0, ${-position}px, 0)`;
        requestAnimationFrame(autoScroll);
      }

      container.style.overflow = 'hidden';
      list.style.willChange = 'transform';
      requestAnimationFrame(autoScroll);
    }
  </script>
</body>

</html>
