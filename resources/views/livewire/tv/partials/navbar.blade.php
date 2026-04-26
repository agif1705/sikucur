<nav class="navbar navbar-expand-lg navbar-light bg-warning">
  <div class="container-fluid">
    <a class="navbar-brand text-white animate__animated animate__pulse animate__infinite infinite" href="#">
      <img src="{{ asset('storage/' . $logo) }}" width="100" alt="Logo" class="d-inline-block align-text-top" />
    </a>

    <span class="fw-bold fs-5">
      PEMERINTAH NAGARI SIKUCUR <br>
      KECAMATAN V KOTO KAMPUNG DALAM <br>
      KABUPATEN PADANG PARIAMAN
    </span>

    <a class="navbar-brand" href="#"></a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTvInfo"
      aria-controls="navbarTvInfo" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse p-2" id="navbarTvInfo">
      <div class="navbar-nav ms-auto">
        <h6
          class="text-white rounded-5 text-sm text-center p-1 fs-3 animate__animated animate__jackInTheBox animate__slower animate__infinite infinite">
          Tv Informasi
          <br>
          <span class="fw-bold text-danger"> Nagari Sikucur </span>
        </h6>
      </div>

      <div class="navbar-nav ms-auto">
        <img class="rounded float-start" style="width: 250px; height: 100px;"
          src="{{ asset('storage/' . $tv->bupati_image) }}" />
      </div>
    </div>
  </div>
</nav>
