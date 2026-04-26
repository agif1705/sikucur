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

            setTimeout(() => {
              this.processQueue();
            }, 500);
          }, 10000);
        }
      }));
    });
  </script>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const carousel = document.querySelector('#galleryCarousel');

      if (!carousel) {
        return;
      }

      console.log('✅ Bootstrap Carousel initialized');

      carousel.addEventListener('mouseenter', function() {
        bootstrap.Carousel.getInstance(carousel)?.pause();
      });

      carousel.addEventListener('mouseleave', function() {
        bootstrap.Carousel.getInstance(carousel)?.cycle();
      });

      carousel.addEventListener('slide.bs.carousel', function(event) {
        const nextImg = event.relatedTarget.querySelector('img');

        if (!nextImg) {
          return;
        }

        nextImg.classList.add('animate__animated', 'animate__fadeIn');

        setTimeout(() => {
          nextImg.classList.remove('animate__fadeIn');
        }, 600);
      });
    });
  </script>

  <script>
    document.addEventListener('livewire:init', () => {
      Livewire.hook('request', ({ fail }) => {
        fail(({ status, preventDefault }) => {
          if (status === 419) {
            preventDefault();
            window.location.reload();
          }
        });
      });
    });
  </script>
@endpush
