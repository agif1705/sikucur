<div class="p-6 rounded-lg shadow-md text-white"
  style="background-color: var(--filament-color-gray-900); color: var(--filament-color-gray-100);">

  <h2 class="text-lg font-bold mb-4">Form Izin Pegawai {{ $users->name }}</h2>

  {{-- Countdown Digital --}}
  <div class="text-center fs-6 text-4xl font-mono tracking-widest mb-6 wrapper">
    <span class="text-1xl">Sisa Waktu pengisian form anda : </span>
    <span class="text-danger text-1xl" id="countdown">00:00:00</span>
  </div>

  {{-- Form --}}
  <form wire:submit.prevent="submit" class="mt-6 w-full max-w-md shadow p-6 rounded-lg">
    {{ $this->form }}
    <x-filament::button type="submit" color="primary" class="mt-2">Kirim</x-filament::button>
  </form>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    let remaining = @json($remainingSeconds);

    function updateCountdown() {
      if (remaining <= 0) {
        document.getElementById('countdown').textContent = 'EXPIRED';
        return;
      }
      let hours = String(Math.floor(remaining / 3600)).padStart(2, '0');
      let minutes = String(Math.floor((remaining % 3600) / 60)).padStart(2, '0');
      let seconds = String(remaining % 60).padStart(2, '0');

      document.getElementById('countdown').textContent = `${hours}:${minutes}:${seconds}`;
      remaining--;
      setTimeout(updateCountdown, 1000);
    }

    updateCountdown();
  });
</script>
