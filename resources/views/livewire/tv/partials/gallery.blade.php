<div class="gallery-container">
  <div class="card-header bg-danger text-light fw-bold text-center py-2">
    <small>Galeri Nagari Sikucur</small>
  </div>

  <div id="galleryCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000" wire:ignore>
    <div class="carousel-inner">
      @forelse ($galeri as $index => $gale)
        <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
          <img src="{{ asset('storage/' . $gale->image) }}" alt="sikucur" class="d-block w-100 carousel-gallery-img">
        </div>
      @empty
        <div class="carousel-item active">
          <div class="no-gallery d-flex align-items-center justify-content-center">
            <h6 class="text-center text-muted mb-0">Tidak ada galeri</h6>
          </div>
        </div>
      @endforelse
    </div>

    @if (count($galeri) > 1)
      <button class="carousel-control-prev" type="button" data-bs-target="#galleryCarousel" data-bs-slide="prev">
        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Previous</span>
      </button>

      <button class="carousel-control-next" type="button" data-bs-target="#galleryCarousel" data-bs-slide="next">
        <span class="carousel-control-next-icon" aria-hidden="true"></span>
        <span class="visually-hidden">Next</span>
      </button>

      <div class="carousel-indicators">
        @foreach ($galeri as $index => $gale)
          <button type="button" data-bs-target="#galleryCarousel" data-bs-slide-to="{{ $index }}"
            class="{{ $index === 0 ? 'active' : '' }}" aria-current="{{ $index === 0 ? 'true' : 'false' }}"
            aria-label="Slide {{ $index + 1 }}"></button>
        @endforeach
      </div>
    @endif
  </div>
</div>
