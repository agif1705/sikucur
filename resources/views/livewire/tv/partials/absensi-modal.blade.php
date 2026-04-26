<div x-data="absensiModal" x-init="initModal">
  <div class="modal fade" id="absensiModal" tabindex="-1" aria-labelledby="absensiModalLabel" aria-hidden="true"
    wire:ignore>
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="absensiModalLabel">Absensi Berhasil <span x-text="nama"></span></h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <h4>
            Cek Status WhatsApp <span x-text="nama"></span> Kami Mengirimkan Pesan Kehadiran Hari Ini Terimakasih
            <br>
            <span class="fw-bold">
              Untuk link izin sudah bisa anda gunakan dengan mengetikan <strong>izin</strong> di Whastapp AdminSikucur
            </span>
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
