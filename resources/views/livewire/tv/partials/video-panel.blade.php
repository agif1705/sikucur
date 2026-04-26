<div class="col-8 h-100 d-flex">
  <div class="card w-100 d-flex flex-column">
    <div class="card-header bg-success flex-shrink-0">
      <marquee class="text-white fs-1 fw-bold">{{ $tv->name }}</marquee>
    </div>

    <div class="card-body p-0 bg-dark flex-grow-1 d-flex">
      <div class="w-100 h-100">
        @if (count($uploadedVideos) > 0)
          @include('livewire.tv.partials.uploaded-video-player')
        @else
          <div class="w-100 h-100 d-flex align-items-center justify-content-center text-white">
            <h3 class="mb-0">Belum ada video aktif</h3>
          </div>
        @endif
      </div>
    </div>

    <div class="card-footer bg-warning position-relative flex-shrink-0">
      <marquee class="text-white fs-4 fw-bold" style="padding-right: 7em;">
        {{ $tv->running_text }}
      </marquee>
      <img src="{{ asset('storage/ppid.png') }}" class="img-thumbnail"
        style="height: 4em; width: 6em; position: absolute; right: 0.5em; bottom: 0.2em;" alt="PPID Logo">
    </div>
  </div>
</div>
