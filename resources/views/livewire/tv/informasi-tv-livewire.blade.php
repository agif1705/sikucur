<div class="vh-100 d-flex flex-column">
  <div x-data="absensiModal" x-init="initModal">
    <!-- Modal -->
    <div class="modal fade" id="absensiModal" tabindex="-1" aria-labelledby="absensiModalLabel" aria-hidden="true"
      wire:ignore>
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="absensiModalLabel">Absensi Berhasil <span x-text="nama"></span></h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body text-center">
            <h4> Cek Status WhatsApp <span x-text="nama"></span> Kami Mengirimkan Pesan Kehadiran Hari Ini Terimakasih
              <br>
              <span class="fw-bold">Untuk link izin sudah bisa anda gunakan dengan mengetikan <strong>izin</strong> di
                Whastapp AdminSikucur</span>
            </h4>
            <div class="d-flex align-items-center gap-2 justify-content-center">
              <h1 class="mb-0" x-text="jam"></h1>
              <span class="badge bg-success fs-5" x-text="status"></span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  @push('scripts')
    <script>
      document.addEventListener('alpine:init', () => {
        Alpine.data('absensiModal', () => ({
          modalInstance: null,
          nama: '',
          jam: '',
          status: '',
          foto: '',
          queue: [],
          isShowing: false,

          initModal() {
            const modalEl = document.getElementById('absensiModal');
            this.modalInstance = new bootstrap.Modal(modalEl, {
              backdrop: 'static',
              keyboard: false
            });

            // Dengarkan event dari Livewire
            window.addEventListener('absenBerhasil', (event) => {
              this.queue.push(event.detail);
              this.processQueue();
            });
          },

          processQueue() {
            if (this.queue.length === 0) {
              this.isShowing = false;
              return;
            }

            this.isShowing = true;
            const data = this.queue.shift();

            this.nama = data.nama;
            this.jam = data.jam;
            this.status = data.status;

            this.modalInstance.show();

            setTimeout(() => {
              this.modalInstance.hide();

              // tunggu animasi modal close selesai (Bootstrap default ±500ms)
              setTimeout(() => {
                this.processQueue();
              }, 500);
            }, 10000);
          }
        }));
      });
    </script>

    <script>
      // Bootstrap Carousel Enhancement
      document.addEventListener('DOMContentLoaded', function() {
        const carousel = document.querySelector('#galleryCarousel');

        if (carousel) {
          console.log('✅ Bootstrap Carousel initialized');

          // Optional: Pause on hover
          carousel.addEventListener('mouseenter', function() {
            bootstrap.Carousel.getInstance(carousel)?.pause();
          });

          carousel.addEventListener('mouseleave', function() {
            bootstrap.Carousel.getInstance(carousel)?.cycle();
          });

          // Add fade animation to carousel items
          carousel.addEventListener('slide.bs.carousel', function(event) {
            const nextImg = event.relatedTarget.querySelector('img');
            if (nextImg) {
              nextImg.classList.add('animate__animated', 'animate__fadeIn');
              setTimeout(() => {
                nextImg.classList.remove('animate__fadeIn');
              }, 600);
            }
          });
        }
      });
    </script>
  @endpush

  <style>
    /* Full height styling */
    html,
    body {
      height: 100% !important;
      margin: 0 !important;
      padding: 0 !important;
      overflow: hidden !important;
    }

    .navbar {
      flex-shrink: 0;
    }

    /* Ensure main content fills remaining space */
    main {
      min-height: 0;
      flex: 1;
    }

    /* Fix card height for video */
    .col-8 .card {
      height: 100% !important;
    }

    .col-8 .card-body {
      flex: 1 !important;
      min-height: 0 !important;
    }

    /* Gallery container */
    .gallery-container {
      flex: 1;
      display: flex;
      flex-direction: column;
      min-height: 300px;
      max-height: 400px;
    }

    /* Bootstrap Carousel styling */
    #galleryCarousel {
      flex: 1;
      width: 100%;
      height: 100%;
      min-height: 250px;
    }

    .carousel-inner {
      height: 100%;
      border-radius: 4px;
      overflow: hidden;
    }

    .carousel-item {
      height: 100%;
    }

    /* Carousel gallery image styling */
    .carousel-gallery-img {
      width: 100% !important;
      height: 100% !important;
      object-fit: contain !important;
      background: #f8f9fa;
      transition: transform 0.3s ease;
      border-radius: 4px;
    }

    /* No gallery fallback */
    .no-gallery {
      width: 100%;
      height: 200px;
      background: #f8f9fa;
      border-radius: 4px;
    }

    /* Carousel controls styling */
    .carousel-control-prev,
    .carousel-control-next {
      width: 8%;
      opacity: 0.7;
      transition: opacity 0.3s ease;
    }

    .carousel-control-prev:hover,
    .carousel-control-next:hover {
      opacity: 1;
    }

    .carousel-control-prev-icon,
    .carousel-control-next-icon {
      background-size: 16px 16px;
    }

    /* Carousel indicators */
    .carousel-indicators {
      margin-bottom: 0.5rem;
    }

    .carousel-indicators [data-bs-target] {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      margin: 0 3px;
      background-color: rgba(255, 255, 255, 0.5);
      border: none;
    }

    .carousel-indicators .active {
      background-color: rgba(255, 255, 255, 1);
      width: 10px;
      height: 10px;
    }

    /* Hover effect */
    .carousel-item:hover .carousel-gallery-img {
      transform: scale(1.02);
    }

    /* Carousel transition effects */
    .carousel-item {
      transition: transform 0.6s ease-in-out;
    }
  </style>

  {{-- bootsrap --}}
  <nav class="navbar navbar-expand-lg navbar-light bg-warning  ">
    <div class="container-fluid ">
      <a class="navbar-brand text-white animate__animated animate__pulse animate__infinite	infinite" href="#">
        <img src="{{ asset('storage/' . $logo) }}" width="100" alt="Logo"
          class="d-inline-block align-text-top" />
      </a>
      <span class="fw-bold fs-5 ">

        PEMERINTAH NAGARI SIKUCUR <br> KECAMATAN V KOTO KAMPUNG DALAM <br>KABUPATEN PADANG PARIAMAN
      </span>

      <a class="navbar-brand" href="#"></a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup"
        aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse p-2" id="navbarNavAltMarkup">
        <div class="navbar-nav ms-auto">
          <h6
            class="text-white rounded-5 text-sm text-center p-1 fs-3 animate__animated animate__jackInTheBox animate__slower  animate__infinite	infinite">
            Tv Informasi
            <br>
            <span class="fw-bold text-danger"> Nagari Sikucur </span>
          </h6>
        </div>
      </div>
      <div class="collapse navbar-collapse " id="navbarNavAltMarkup">
        <div class="navbar-nav ms-auto">
          <img class="rounded float-start  " style="width: 250px; height: 100px;"
            src="{{ asset('storage/' . $tv->bupati_image) }}" />
        </div>
      </div>
    </div>
  </nav>
  <main class="flex-grow-1">
    <div class="container-fluid h-100 w-100">
      <div class="row h-100 g-0">
        <div class="col-2 mt-0 rounded bg-info h-100 d-flex flex-column">
          <h6 class="fs-5 fw-bold mb-1 text-center badge rounded-pill">E-Absensi {{ $tvNow }}
          </h6>
          <div id="absensi-container" wire:ignore.self>
            <ul class="list-group mt-1 rounded w-100 p-2">
              @forelse ($users as $item)
                {{-- @dd($users) --}}
                <li
                  class="list-group-item d-flex justify-content-between align-items-center mb-1 border-1 rounded rounded-4">
                  <div class="d-flex align-items-center">
                    <img class="avatar @if ($users->count() > 1) avatar-lg @else avatar-xl @endif "
                      src="{{ asset('storage/' . $item['image'] ?? 'default-avatar.png') }}" />
                    <div class="ms-3">
                      <p class="fs-6 fw-bold mb-0 text-start">
                        {{ Str::ucfirst($item['name']) }}</p>
                      <p class="text-muted mb-0 fs-sm text-start">
                        {{ Str::ucfirst($item['jabatan']) }}</p>
                      @if ($item['absensi_by'] === 'Fingerprint')
                        <p class="fw-bold text-muted mb-0 fs-md text-start ">
                          Masuk :
                          {{ $item['time_only'] }}
                          <span
                            class="fw-bold {{ $item['is_late'] ? 'text-danger' : 'text-success' }} text-end fst-italic">

                            {{ $item['is_late'] ? 'Terlambat' : 'Ontime' }}
                            <br>{{ $item['absensi_by'] }} - {{ $item['status'] }}
                          </span>
                        </p>
                      @endif
                      @if ($item['absensi_by'] === 'web')
                        <p class="fw-bold text-muted mb-0 fs-md text-start ">
                          <span class="fw-bold text-success text-end fst-italic">
                            @switch($item['status'])
                              @case('HDDD')
                                Dinas Dalam Daerah
                              @break

                              @case('HDLD')
                                Dinas Luar Daerah
                              @break

                              @case('S')
                                Sakit
                              @break

                              @case('I')
                                Izin
                              @break

                              @default
                                {{ $item['status'] }}
                            @endswitch
                            : {{ $item['absensi_by'] }} - {{ $item['time_only'] }}
                          </span>
                        </p>
                      @endif
                      @if ($item['absensi_by'] === null)
                        @if ($item['jabatan'] === 'WaliNagari')
                          <p class="fw-bold text-muted mb-0 fs-md text-start ">
                            <span class="fw-bold text-success text-end fst-italic">Sedang diluar Kantor</span>
                          </p>
                        @else
                          <p class="fw-bold text-muted mb-0 fs-md text-start ">
                            <span class="fw-bold text-danger text-end fst-italic">Tidak Masuk</span>
                          </p>
                        @endif
                      @endif
                    </div>
                  </div>
                </li>
                @empty
                  <li class="list-group-item text-center">Tidak ada data absensi</li>
                @endforelse

              </ul>
            </div>

          </div>
          <!-- video youtube -->
          <div class="col-8 h-100 d-flex">
            <div class="card w-100 d-flex flex-column">
              <!-- Header -->
              <div class="card-header bg-success flex-shrink-0">
                <marquee class="text-white fs-1 fw-bold">{{ $tv->name }}</marquee>
              </div>
              <!-- Video Body -->
              <div class="card-body p-0 bg-dark flex-grow-1 d-flex">
                <div class="w-100 h-100">
                  <iframe
                    src="https://www.youtube.com/embed/{{ $videoId }}?playlist={{ $playlistStr }}&loop=1&autoplay=1&mute=0&rel=0&modestbranding=1"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen class="w-100 h-100">
                  </iframe>
                </div>
              </div>
              <!-- Footer -->
              <div class="card-footer bg-warning position-relative flex-shrink-0">
                <img src="{{ asset('storage/wali_tv.png') }}" alt="..."
                  class="position-absolute start-25 translate-middle-x" style="height: 20em; width: 18em; bottom: 0.0em;">
                <marquee class="text-white fs-4 fw-bold" style="padding-right: 7em;">
                  {{ $tv->running_text }}
                </marquee>
                <img src="{{ asset('storage/ppid.png') }}" class="img-thumbnail"
                  style="height: 4em; width: 6em; position: absolute; right: 0.5em; bottom: 0.2em;" alt="PPID Logo">
              </div>
            </div>
          </div>
          <div class="col-2 bg-light h-100 d-flex flex-column text-center">

            <div class="row mt-3 mb-1 bg-light p-2">
              <div class="col">
                <a href="/path/to/image1.jpg" data-toggle="lightbox">
                  <img src="{{ asset('storage/' . $tv->bamus_image) }}" class="img-fluid w-100">
                </a>
              </div>
            </div>
            <!-- Bootstrap Carousel -->
            <div class="gallery-container">
              <div class="card-header bg-danger text-light fw-bold text-center py-2">
                <small>Galeri Nagari Sikucur</small>
              </div>
              <div id="galleryCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3000"
                wire:ignore>
                <div class="carousel-inner">
                  @forelse ($galeri as $index => $gale)
                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                      <img src="{{ asset('storage/' . $gale->image) }}" alt="sikucur"
                        class="d-block w-100 carousel-gallery-img">
                    </div>
                  @empty
                    <div class="carousel-item active">
                      <div class="no-gallery d-flex align-items-center justify-content-center">
                        <h6 class="text-center text-muted mb-0">Tidak ada galeri</h6>
                      </div>
                    </div>
                  @endforelse
                </div>

                <!-- Controls -->
                @if (count($galeri) > 1)
                  <button class="carousel-control-prev" type="button" data-bs-target="#galleryCarousel"
                    data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                  </button>
                  <button class="carousel-control-next" type="button" data-bs-target="#galleryCarousel"
                    data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                  </button>

                  <!-- Indicators -->
                  <div class="carousel-indicators">
                    @foreach ($galeri as $index => $gale)
                      <button type="button" data-bs-target="#galleryCarousel" data-bs-slide-to="{{ $index }}"
                        class="{{ $index === 0 ? 'active' : '' }}"
                        aria-current="{{ $index === 0 ? 'true' : 'false' }}"
                        aria-label="Slide {{ $index + 1 }}"></button>
                    @endforeach
                  </div>
                @endif
              </div>
            </div>
            <!-- foto wali -->
            <div class="bg-dark mb-1 mt-3 p-2 flex-grow-1 d-flex align-items-center justify-content-center"
              style="height: 25%;">
              <h1 class="text-center text-white shadow">Coming soon CCTV Area Public</h1>
            </div>
          </div>
          <!-- Carousel wrapper -->
        </div>
      </div>
    </main>
  </div>
