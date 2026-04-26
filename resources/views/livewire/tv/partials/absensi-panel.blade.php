<div class="col-2 mt-0 rounded bg-info h-100 d-flex flex-column position-relative">
  <h6 class="fs-5 fw-bold mb-1 text-center badge rounded-pill">E-Absensi {{ $tvNow }}</h6>

  <div id="absensi-container" wire:ignore.self>
    <ul class="list-group mt-1 rounded w-100 p-2">
      @forelse ($users as $item)
        <li class="list-group-item d-flex justify-content-between align-items-center mb-1 border-1 rounded rounded-4">
          <div class="d-flex align-items-center">
            <img class="avatar @if ($users->count() > 1) avatar-lg @else avatar-xl @endif"
              src="{{ asset('storage/' . ($item['image'] ?? 'default-avatar.png')) }}" />

            <div class="ms-3">
              <p class="fs-6 fw-bold mb-0 text-start">{{ Str::ucfirst($item['name']) }}</p>
              <p class="text-muted mb-0 fs-sm text-start">{{ Str::ucfirst($item['jabatan']) }}</p>

              @include('livewire.tv.partials.absensi-status', ['item' => $item])
            </div>
          </div>
        </li>
      @empty
        <li class="list-group-item text-center">Tidak ada data absensi</li>
      @endforelse
    </ul>
  </div>

  <div class="position-absolute w-100" style="bottom: 0; left: 0; right: 0; padding: 0 0.25rem;">
    <div class="position-relative d-flex justify-content-start">
      <div class="flex-shrink-0">
        <img src="{{ asset('storage/wali_tv.png') }}" alt="Foto Wali Nagari"
          style="height: 13em; width: 10em; object-fit: contain;">
      </div>

      <div class="position-absolute speech-bubble"
        style="top: 20%; left: 8.5em; transform: translateX(-15%); min-width: 0; max-width: calc(100% - 9em); z-index: 10;">
        <div class="bg-opacity-90 p-1 rounded-2" style="backdrop-filter: blur(3px);">
          <h4 class="fw-bold text-white mb-1">Sikucur Bangkit</h4>
          <h5 class="fw-bold text-white mb-1"
            style="line-height: 1.0; text-shadow: 1px 1px 2px rgba(0,0,0,0.8); word-wrap: break-word;">
            "Melayani Dengan Hati, <br>Membangun Dengan Visi"
          </h5>
        </div>
        <div class="position-absolute"
          style="left: -6px; top: 50%; transform: translateY(-50%); width: 0; height: 0; border-top: 6px solid transparent; border-bottom: 6px solid transparent; border-right: 6px solid rgba(13, 253, 65, 0.9);">
        </div>
      </div>
    </div>
  </div>
</div>
