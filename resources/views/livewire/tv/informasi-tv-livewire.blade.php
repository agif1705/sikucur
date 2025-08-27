<div>
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
  @endpush
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
  <main class="flex-grow-1 d-flex align-items-center justify-content-center">
    <div class="container-fluid text-center w-100 h-100"> <!-- Tambahkan h-100 di sini -->
      <div class="row align-items-start h-100"> <!-- Tambahkan h-100 di sini -->
        <div class="col-2 mt-0 rounded bg-info h-100 d-flex flex-column">
          <h6 class="fs-5 fw-bold mb-1 text-start text-center  badge rounded-pill ">E-Absensi {{ $tvNow }}
          </h6>
          <div id="absensi-container" wire:ignore.self>
            <ul class="list-group mt-1 rounded w-100 ">
              @forelse ($users as $item)

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
                      @if ($item['status'])
                        <p class="fw-bold text-muted mb-0 fs-md text-start ">
                          Masuk :
                          {{ $item['time_only'] }}
                          @if ($item['is_late'])
                            <span class="fw-bold text-danger text-end fst-italic">Terlambat</span>
                          @else
                            <span class="fw-bold text-success text-end">
                              @if ($item['status'])
                                Tepat Waktu
                                <br>{{ $item['absensi_by'] }}
                              @else
                                {{ $item['status'] }}
                              @endif
                            </span>
                          @endif
                        </p>
                      @elseif ($item['status'] === null)
                        <p class="fw-bold text-muted mb-0 fs-md text-start ">
                          <span class="fw-bold text-danger text-end fst-italic">Tidak Masuk</span>
                        </p>
                      @else
                        <p class="fw-bold text-muted mb-0 fs-md text-start ">
                          <span class="fw-bold text-success text-end fst-italic">{{ $item['status'] }}</span>
                        </p>
                      @endif

                    </div>
                  </div>
                </li>
              @empty
                <li class="list-group-item text-center">Tidak ada data absensi</li>
              @endforelse

            </ul>
          </div>
          <div class="bg-dark mb-1 mt-3 flex-grow-1 d-flex align-items-center justify-content-center"
            style="height: 100%;">
            <h1 class="text-center text-white shadow">Coming soon CCTV Area Public</h1>
          </div>
        </div>
        <!-- video youtube -->
        <div class="col-8" style="height: 85vh;"> <!-- Sesuaikan height sesuai kebutuhan -->
          <div class="card h-100">
            <!-- Header -->
            <div class="card-header bg-success">
              <marquee class="text-white fs-1 fw-bold">{{ $tv->name }}</marquee>
            </div>
            <!-- Video Body -->
            <div class="card-body p-0 bg-dark flex-grow-1">
              <div style="width:100%; height:100%;">
                <iframe
                  src="https://www.youtube.com/embed/{{ $videoId }}?playlist={{ $playlistStr }}&loop=1&autoplay=1&mute=0&rel=0&modestbranding=1"
                  frameborder="0"
                  allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                  allowfullscreen style="width:100%; height:100%;">
                </iframe>
              </div>
            </div>
            <!-- Footer -->
            <div class="card-footer bg-warning position-relative">
              <marquee class="text-white fs-4 fw-bold" style="padding-right: 7em;">
                {{ $tv->running_text }}
              </marquee>
              <img src="{{ asset('storage/ppid.png') }}" class="img-thumbnail"
                style="height: 4em; width: 6em; position: absolute; right: 0.5em; bottom: 0.2em;" alt="PPID Logo">
            </div>
          </div>
        </div>
        <div class="col-2 bg-light h-100 d-flex flex-column text-center">
          <!-- foto wali -->

          <div class="row row-cols-1 bg-light row-cols-sm-1 g-1 mt-1">
            <div class="col-sm-12 bg-warning justify-center">
              <div class="card-body">
                <span class="card-text">Wali Nagari Sikucur</span>

                <img src="{{ asset('storage/' . $tv->wali_nagari_image) }}" class="card-img-top p-2 w-100 h-100"
                  alt="...">
                <span class="card-text fs-3 fw-bold">{{ $tv->wali_nagari }}</span>

              </div>
            </div>
          </div>
          <div class="row mt-3 mb-1 bg-light p-2">
            <div class="col">
              <a href="/path/to/image1.jpg" data-toggle="lightbox">
                <img src="{{ asset('storage/' . $tv->bamus_image) }}" class="img-fluid w-100">
              </a>
            </div>
          </div>
          <!-- Carousel wrapper -->
          <div class="container w-100 h-100 justify-center">
            <div class="card-header bg-danger text-light fw-bold">
              Galeri Nagari Sikucur
            </div>
            <div class="swiper mySwiper position-relative" wire:ignore>
              <div class="swiper-wrapper">
                @foreach ($galeri as $gale)
                  <div class="swiper-slide">
                    <img src="{{ asset('storage/' . $gale->image) }}" alt="sikucur" class="w-100 h-25">
                  </div>
                @endforeach
              </div>

            </div>
          </div>
        </div>
        <!-- Carousel wrapper -->
      </div>
    </div>
  </main>
</div>
