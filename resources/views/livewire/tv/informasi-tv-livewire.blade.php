<div>
    {{-- bootsrap --}}
    <nav class="navbar navbar-expand-lg navbar-light bg-warning  ">
        <div class="container-fluid ">
            <a class="navbar-brand text-white animate__animated animate__pulse animate__infinite	infinite" href="#">
                <img src="{{ asset('storage/' . $this->logo) }}" width="100" alt="Logo"
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
                        class="text-white rounded-5 text-sm text-center p-2 fs-3 animate__animated animate__jackInTheBox animate__slower  animate__infinite	infinite">
                        Tv Informasi
                        <br>
                        <span class="fw-bold text-danger"> Nagari Sikucur</span>
                    </h6>
                </div>
            </div>
            <div class="collapse navbar-collapse " id="navbarNavAltMarkup">
                <div class="navbar-nav ms-auto">
                    <img class="rounded float-start  " style="width: 250px; height: 100px;"
                        src="{{ asset('storage/' . $this->tv->bupati_image) }}" />
                </div>
            </div>

        </div>
    </nav>
    <main class="flex-grow-1 d-flex align-items-center justify-content-center">
        <div class="container-fluid text-center w-100 h-100"> <!-- Tambahkan h-100 di sini -->
            <div class="row align-items-start h-100"> <!-- Tambahkan h-100 di sini -->
                <div class="col-2 mt-2 rounded bg-info h-100 d-flex flex-column position-relative">
                    <span
                        class="position-absolute top-0 start-50 w-50  translate-middle badge rounded-pill text-bg-success">
                        <p class="fs-5 fw-bold mb-0 text-start text-center">E-Absensi </p>
                    </span>

                    <div class="clas" wire:poll.30s="refreshData">

                        <ul class="list-group mt-6 ">
                            <p class="fs-6 fw-bold mb-0 text-start text-light text-center">{{ $this->tvNow }}</p>
                            @foreach ($this->users as $item)
                                <li
                                    class="list-group-item d-flex justify-content-between align-items-center mb-1 border-1">
                                    <div class="d-flex align-items-center">

                                        <img class="avatar 
                                            @if ($this->users->count() > 8) avatar-lg @else avatar-xl @endif "
                                            src="{{ asset('storage/' . $item->user->image) }}" />
                                        <div class="ms-3">
                                            <p class="fs-6 fw-bold mb-0 text-start">
                                                {{ Str::ucfirst($item->user->name) }}</p>
                                            <p class="text-muted mb-0 fs-sm text-start">
                                                {{ Str::ucfirst($item->user->jabatan->name) }}</p>
                                            <p class="fw-bold text-muted mb-0 fs-md text-start ">Masuk :
                                                {{ $item->time_only }}
                                                @if ($item->is_late)
                                                    <span
                                                        class="fw-bold text-danger text-end fst-italic fs-5">Terlambat</span>
                                                @else
                                                    <span class="fw-bold text-success text-end">OnTime</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </li>
                            @endforeach

                        </ul>
                    </div>
                </div>
                <!-- video youtube -->
                <div class="col-8" style="height: 90vh;"> <!-- Sesuaikan height sesuai kebutuhan -->
                    <div class="card h-100">
                        <!-- Header -->
                        <div class="card-header bg-success">
                            <marquee class="text-white fs-1 fw-bold">{{ $this->tv->name }}</marquee>
                        </div>

                        <!-- Video Body -->
                        <div class="card-body p-0 bg-dark flex-grow-1">
                            <div class="ratio ratio-16x9 h-100">
                                {{-- <iframe
                                    src="https://www.youtube.com/embed/{{ $this->tv->video }}?playlist={{ $this->tv->video }}&loop=1"
                                    allowfullscreen>
                                </iframe> --}}
                                <iframe width="560" height="315"
                                    src="https://www.youtube.com/embed/Lb4AwReHYxQ?playlist=RpTfJV4ux1c,V5s36YgfWv8,Sz61lW5trNQ,6vIqikH2QvY,-nbJgfkgSg8,
                                    s6vac3hP6yM,k9bCgz5xTms,j-vOeGOCKio,coz56CHNjjE,jXNSJnxkXeE
                                    &loop=1"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen>
                                </iframe>
                            </div>
                        </div>
                        <!-- Footer -->
                        <div class="card-footer bg-warning">
                            <div class="col-sm-12 bg-warning justify-center h-100">
                                <marquee class="text-white fs-4 fw-bold">{{ $this->tv->running_text }}
                                </marquee>
                                <img src="{{ asset('storage/ppid.png') }}" class="img-thumbnail p-1"
                                    style="height: 4em; width: 6em; position: absolute; right: 0; bottom: 0;"
                                    alt="PPID Logo">

                            </div>

                        </div>
                    </div>
                </div>
                <div class="col-2 bg-light h-100 d-flex flex-column text-center">
                    <!-- foto wali -->

                    <div class="row row-cols-1 bg-light row-cols-sm-1 g-1 mt-1">
                        <div class="col-sm-12 bg-warning justify-center">
                            <div class="card-body">
                                <span class="card-text">Wali Nagari Sikucur</span>

                                <img src="{{ asset('storage/' . $this->tv->wali_nagari_image) }}"
                                    class="card-img-top p-2 w-100 h-100" alt="...">
                                <span class="card-text fs-3 fw-bold">{{ $this->tv->wali_nagari }}</span>

                            </div>
                        </div>
                        {{-- <div class="col-sm-6 justify-center">
                            <div class="card-body">
                                <img src="{{ asset('storage/' . $this->tv->wali_nagari_image) }}"
                                    class="card-img-top p-2 w-100 h-100" alt="...">
                                <span class="card-text">Wali Nagari Sikucur</span>

                            </div>
                        </div> --}}

                    </div>
                    <div class="row mt-3 mb-1 bg-light p-2">
                        <div class="col">
                            <a href="/path/to/image1.jpg" data-toggle="lightbox">
                                <img src="{{ asset('storage/' . $this->tv->bamus_image) }}" class="img-fluid w-100">
                            </a>
                        </div>
                    </div>
                    {{-- <div class="row mt-1 mb-2 bg-light p-2 ">
                        <div class="card">
                            <div class="card-header bg-danger text-light fw-bold">
                                Agenda Nagari Sikucur
                            </div>
                            <div class="card-body">
                                <p class="card-text">spesial agenda
                                </p>
                            </div>
                        </div>
                    </div> --}}
                    <!-- Carousel wrapper -->
                    <div class="container w-100 h-100 justify-center">
                        <div class="card-header bg-danger text-light fw-bold">
                            Galeri Nagari Sikucur
                        </div>
                        <div class="swiper mySwiper position-relative" wire:ignore>
                            <div class="swiper-wrapper">
                                @foreach ($this->galeri as $gale)
                                    <div class="swiper-slide">
                                        <img src="{{ asset('storage/' . $gale->image) }}" alt="sikucur"
                                            class="w-100 h-25">
                                    </div>
                                @endforeach
                            </div>

                        </div>
                    </div>
                </div>
                <!-- Carousel wrapper -->
            </div>
        </div>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        new Swiper('.mySwiper', {
            effect: 'coverflow',
            coverflowEffect: {
                rotate: 30,
                stretch: 0,
                depth: 100,
                modifier: 1,
                slideShadows: true
            },
            autoplay: {
                delay: 5000,
                disableOnInteraction: false,
            },
            loop: true,
        });
    });
</script>
</div>
