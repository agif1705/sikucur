<div class="vh-100 d-flex flex-column">
  @include('livewire.tv.partials.absensi-modal')
  @include('livewire.tv.partials.scripts')
  @include('livewire.tv.partials.styles')

  @include('livewire.tv.partials.navbar')

  <main class="flex-grow-1">
    <div class="container-fluid h-100 w-100">
      <div class="row h-100 g-0">
        @include('livewire.tv.partials.absensi-panel')
        @include('livewire.tv.partials.video-panel')
        @include('livewire.tv.partials.right-panel')
      </div>
    </div>
  </main>
</div>
