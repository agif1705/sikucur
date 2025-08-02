<div>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />


    <x-filament::section>
        <x-slot name="heading" icon="heroicon-o-user" icon-size="sm" icon-color="info">
            Absensi Dinas Luar Kantor
        </x-slot>
        <x-slot name="description">
            Absensi ini untuk dinas luar kantor <strong>Sebagai History Laporan Absensi</strong>
        </x-slot>

        <form wire:submit="create">

            <div class="container mx-auto max-w-sm">
                {{ $this->form }}

                <div class="grid grid-cols-1 gap-6 mb-6 mt-2 ">

                    <div class="bg-gray-100 p-1 rounded-lg">
                        <h4 class="text-l font-bold mb-2">
                            Jam Masuk : {{ $kehadiran ? $kehadiran->start_time : '-' }}
                        </h4>
                        <h4 class="text-l font-bold mb-2">
                            Jam keluar : {{ $kehadiran ? $kehadiran->end_time : '-' }}
                        </h4>
                    </div>
                    <div class="">
                        <div id="map" class=" w-full h-96" wire:ignore></div>
                        @if (session()->has('error'))
                            <div class="bg-red-100 border border-red-400 text-red-700 px-2 py-3 rounded relative"
                                role="alert">
                                <span class="block sm:inline">{{ session('error') }}</span>
                            </div>
                        @endif


                        @empty($check_kehadiran)
                            <form class="row g-3" wire:submit="store" enctype="multipart/form-data">
                                <x-filament::button size="sm" color="info" onclick="tagLocation()">
                                    Cek Lokasi
                                </x-filament::button>
                                @if ($insideRadius)
                                    <button type="submit"
                                        class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded">
                                        Absen Sekarang
                                    </button>
                                @endif
                            </form>
                        @endempty

                    </div>
                </div>
            </div>
            <x-filament::button type="submit" size="xs" color="success" icon="heroicon-m-sparkles">
                Simpan
            </x-filament::button>

        </form>

    </x-filament::section>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        let map;
        let lat;
        let lng;
        const kantor = [{{ $jadwal->nagari->latitude }}, {{ $jadwal->nagari->longitude }}];
        const radius = 10;
        let component;
        let marker;
        document.addEventListener('livewire:initialized', function() {
            component = @this;
            map = L.map('map').setView([{{ $jadwal->nagari->latitude }}, {{ $jadwal->nagari->longitude }}], 18);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
            const circle = L.circle(kantor, {
                radius: radius,
                color: 'red',
                fillColor: '#f03',
                fillOpacity: 0.5
            }).addTo(map);
        })

        function tagLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function(position) {
                    lat = position.coords.latitude;
                    lng = position.coords.longitude;
                    console.log(lat, lng);
                    if (marker) {
                        map.removeLayer(marker);
                    }

                    marker = L.marker([lat, lng]).addTo(map);
                    map.setView([lat, lng], 15);

                    if (isWithInRadius(lat, lng, kantor, radius)) {
                        component.set('insideRadius', true);
                        component.set('latitude', lat);
                        component.set('longitude', lng);
                    }
                })
            } else {
                alert('Tidak bisa get location');
            }
        }

        function isWithInRadius(lat, lng, kantor, radius) {
            let distance = map.distance([lat, lng], kantor);
            return distance <= radius;
        }
    </script>
</div>
