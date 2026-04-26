<div class="col-2 bg-light h-100 d-flex flex-column text-center">
  <div class="row mt-3 mb-1 bg-light p-2">
    <div class="col">
      <a href="/path/to/image1.jpg" data-toggle="lightbox">
        <img src="{{ asset('storage/' . $tv->bamus_image) }}" class="img-fluid w-100">
      </a>
    </div>
  </div>

  @include('livewire.tv.partials.gallery')

  <div class="bg-dark mb-1 mt-3 p-2 flex-grow-1 d-flex align-items-center justify-content-center"
    style="height: 25%;">
    <h1 class="text-center text-white shadow">Coming soon CCTV Area Public</h1>
  </div>
</div>
