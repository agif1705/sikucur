<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>TV Android Informasi Nagari</title>
	<link rel="shortcut icon" href="{{ asset('logo/logo.png') }}">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="https://cdn.jsdelivr.net/npm/fastbootstrap@2.2.0/dist/css/fastbootstrap.min.css" rel="stylesheet"
		integrity="sha256-V6lu+OdYNKTKTsVFBuQsyIlDiRWiOmtC8VQ8Lzdm2i4=" crossorigin="anonymous">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

	<style>
		html,
		body {
			height: 100% !important;
			margin: 0 !important;
			padding: 0 !important;
			overflow: hidden !important;
		}

		.navbar {
			flex-shrink: 0;
		}

		main {
			min-height: 0;
			flex: 1;
		}

		.col-8 .card {
			height: 100% !important;
		}

		.col-8 .card-body {
			flex: 1 !important;
			min-height: 0 !important;
		}

		#absensi-container {
			height: 65vh;
			overflow: hidden;
			position: relative;
		}

		#absensi-list {
			will-change: transform;
			transition: transform 0.5s linear;
		}

		.gallery-container {
			flex: 1;
			display: flex;
			flex-direction: column;
			min-height: 300px;
			max-height: 400px;
		}

		#galleryCarousel {
			flex: 1;
			width: 100%;
			height: 100%;
			min-height: 250px;
		}

		.carousel-inner,
		.carousel-item {
			height: 100%;
		}

		.carousel-inner {
			border-radius: 4px;
			overflow: hidden;
		}

		.carousel-gallery-img {
			width: 100% !important;
			height: 100% !important;
			object-fit: contain !important;
			background: #f8f9fa;
			transition: transform 0.3s ease;
			border-radius: 4px;
		}

		.no-gallery {
			width: 100%;
			height: 200px;
			background: #f8f9fa;
			border-radius: 4px;
		}

		.carousel-control-prev,
		.carousel-control-next {
			width: 8%;
			opacity: 0.7;
			transition: opacity 0.3s ease;
		}

		.carousel-control-prev:hover,
		.carousel-control-next:hover {
			opacity: 1;
		}

		.carousel-control-prev-icon,
		.carousel-control-next-icon {
			background-size: 16px 16px;
		}

		.carousel-indicators {
			margin-bottom: 0.5rem;
		}

		.carousel-indicators [data-bs-target] {
			width: 8px;
			height: 8px;
			border-radius: 50%;
			margin: 0 3px;
			background-color: rgba(255, 255, 255, 0.5);
			border: none;
		}

		.carousel-indicators .active {
			background-color: rgba(255, 255, 255, 1);
			width: 10px;
			height: 10px;
		}

		.carousel-item:hover .carousel-gallery-img {
			transform: scale(1.02);
		}

		.carousel-item {
			transition: transform 0.6s ease-in-out;
		}

		@keyframes speechFade {
			0% {
				opacity: 0;
				transform: translateX(-15%) scale(0.8);
			}

			25%,
			75% {
				opacity: 1;
				transform: translateX(-15%) scale(1);
			}

			100% {
				opacity: 0;
				transform: translateX(-15%) scale(0.8);
			}
		}

		.speech-bubble {
			animation: speechFade 6s ease-in-out infinite;
		}

		.avatar-lg {
			width: 56px;
			height: 56px;
			border-radius: 50%;
			object-fit: cover;
		}

		.avatar-xl {
			width: 72px;
			height: 72px;
			border-radius: 50%;
			object-fit: cover;
		}
	</style>
</head>

<body class="d-flex flex-column">
	<nav class="navbar navbar-expand-lg navbar-light bg-warning">
		<div class="container-fluid">
			<a class="navbar-brand text-white animate__animated animate__pulse animate__infinite infinite" href="#">
				<img id="logo-nagari" src="{{ asset('storage/default-avatar.png') }}" width="100" alt="Logo"
					class="d-inline-block align-text-top" />
			</a>

			<span class="fw-bold fs-5">
				PEMERINTAH NAGARI SIKUCUR <br>
				KECAMATAN V KOTO KAMPUNG DALAM <br>
				KABUPATEN PADANG PARIAMAN
			</span>

			<div class="navbar-nav ms-auto">
				<h6
					class="text-white rounded-5 text-sm text-center p-1 fs-3 animate__animated animate__jackInTheBox animate__slower animate__infinite infinite">
					Tv Informasi
					<br>
					<span class="fw-bold text-danger" id="title-nagari"> Nagari Sikucur </span>
				</h6>
			</div>

			<div class="navbar-nav ms-auto">
				<img class="rounded float-start" style="width: 250px; height: 100px;" id="bupati-image"
					src="{{ asset('storage/default-avatar.png') }}" alt="Bupati">
			</div>
		</div>
	</nav>

	<main class="flex-grow-1">
		<div class="container-fluid h-100 w-100">
			<div class="row h-100 g-0">
				<div class="col-2 mt-0 rounded bg-info h-100 d-flex flex-column position-relative">
					<h6 class="fs-5 fw-bold mb-1 text-center badge rounded-pill" id="absensi-date">E-Absensi</h6>

					<div id="absensi-container">
						<ul class="list-group mt-1 rounded w-100 p-2" id="absensi-list">
							<li class="list-group-item text-center">Memuat data absensi...</li>
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

				<div class="col-8 h-100 d-flex">
					<div class="card w-100 d-flex flex-column">
						<div class="card-header bg-success flex-shrink-0">
							<marquee class="text-white fs-1 fw-bold" id="tv-name">TV Informasi</marquee>
						</div>

						<div class="card-body p-0 bg-dark flex-grow-1 d-flex">
							<div class="w-100 h-100" id="video-root">
								<div class="w-100 h-100 d-flex align-items-center justify-content-center text-white">
									<h3 class="mb-0">Memuat video...</h3>
								</div>
							</div>
						</div>

						<div class="card-footer bg-warning position-relative flex-shrink-0">
							<marquee class="text-white fs-4 fw-bold" style="padding-right: 7em;" id="running-text">
								Memuat running text...
							</marquee>
							<img src="{{ asset('storage/ppid.png') }}" class="img-thumbnail"
								style="height: 4em; width: 6em; position: absolute; right: 0.5em; bottom: 0.2em;" alt="PPID Logo">
						</div>
					</div>
				</div>

				<div class="col-2 bg-light h-100 d-flex flex-column text-center">
					<div class="row mt-3 mb-1 bg-light p-2">
						<div class="col">
							<img id="bamus-image" src="{{ asset('storage/default-avatar.png') }}" class="img-fluid w-100" alt="Bamus">
						</div>
					</div>

					<div class="gallery-container">
						<div class="card-header bg-danger text-light fw-bold text-center py-2">
							<small>Galeri Nagari Sikucur</small>
						</div>
						<div id="gallery-root"></div>
					</div>

					<div class="bg-dark mb-1 mt-3 p-2 flex-grow-1 d-flex align-items-center justify-content-center"
						style="height: 25%;">
						<h1 class="text-center text-white shadow">Coming soon CCTV Area Public</h1>
					</div>
				</div>
			</div>
		</div>
	</main>

	<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
	<script type="module">
		import { createClient } from 'https://cdn.jsdelivr.net/npm/@supabase/supabase-js/+esm';

		const slug = @json($slug);
		const apiBase = @json(url('/api/tv'));

		let carouselInstance = null;
		let videoList = [];
		let videoIndex = 0;
		let realtimeCleanup = null;

		function ucFirst(text) {
			if (!text) return '-';
			return text.charAt(0).toUpperCase() + text.slice(1);
		}

		function statusHtml(item) {
			const absensiBy = item.absensi_by;
			const statusLabel = item.status_label || item.status || '-';
			const timeOnly = item.time_only || '-';
			const isWeb = String(absensiBy || '').toLowerCase() === 'web';
			const isWali = item.jabatan === 'WaliNagari';

			if (absensiBy === 'Fingerprint') {
				if (isWali) {
					return '<p class="fw-bold text-muted mb-0 fs-md text-start">Masuk : <span class="fw-bold text-success text-end fst-italic">Ada di Kantor</span></p>';
				}

				const statusClass = item.is_late ? 'text-danger' : 'text-success';
				const lateLabel = item.is_late ? 'Terlambat' : 'Ontime';
				return `<p class="fw-bold text-muted mb-0 fs-md text-start">Masuk : ${timeOnly} <span class="fw-bold ${statusClass} text-end fst-italic">${lateLabel}<br>${absensiBy} - ${statusLabel}</span></p>`;
			}

			if (isWeb) {
				return `<p class="fw-bold text-muted mb-0 fs-md text-start"><span class="fw-bold text-success text-end fst-italic">${statusLabel} : WhatsApp - ${timeOnly}</span></p>`;
			}

			if (isWali) {
				return '<p class="fw-bold text-muted mb-0 fs-md text-start"><span class="fw-bold text-success text-end fst-italic">Sedang di luar Kantor</span></p>';
			}

			return '<p class="fw-bold text-muted mb-0 fs-md text-start"><span class="fw-bold text-danger text-end fst-italic">Tidak Masuk</span></p>';
		}

		function renderAbsensi(items) {
			const list = document.getElementById('absensi-list');
			if (!list) return;

			if (!items || items.length === 0) {
				list.innerHTML = '<li class="list-group-item text-center">Tidak ada data absensi</li>';
				return;
			}

			const avatarClass = items.length > 1 ? 'avatar-lg' : 'avatar-xl';

			list.innerHTML = items.map((item) => `
				<li class="list-group-item d-flex justify-content-between align-items-center mb-1 border-1 rounded rounded-4">
					<div class="d-flex align-items-center">
						<img class="avatar ${avatarClass}" src="${item.image_url || '{{ asset('storage/default-avatar.png') }}'}" alt="Avatar"/>
						<div class="ms-3">
							<p class="fs-6 fw-bold mb-0 text-start">${ucFirst(item.name)}</p>
							<p class="text-muted mb-0 fs-sm text-start">${ucFirst(item.jabatan)}</p>
							${statusHtml(item)}
						</div>
					</div>
				</li>
			`).join('');
		}

		function renderGallery(items) {
			const root = document.getElementById('gallery-root');
			if (!root) return;

			if (!items || items.length === 0) {
				root.innerHTML = `
					<div class="carousel slide" id="galleryCarousel">
						<div class="carousel-inner">
							<div class="carousel-item active">
								<div class="no-gallery d-flex align-items-center justify-content-center">
									<h6 class="text-center text-muted mb-0">Tidak ada galeri</h6>
								</div>
							</div>
						</div>
					</div>
				`;
				return;
			}

			const inner = items.map((item, index) => `
				<div class="carousel-item ${index === 0 ? 'active' : ''}">
					<img src="${item.image_url}" alt="galeri" class="d-block w-100 carousel-gallery-img">
				</div>
			`).join('');

			const indicators = items.map((_, index) => `
				<button type="button" data-bs-target="#galleryCarousel" data-bs-slide-to="${index}"
					class="${index === 0 ? 'active' : ''}" aria-current="${index === 0 ? 'true' : 'false'}"
					aria-label="Slide ${index + 1}"></button>
			`).join('');

			root.innerHTML = `
				<div id="galleryCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="4000">
					<div class="carousel-inner">${inner}</div>
					${items.length > 1 ? `
					<button class="carousel-control-prev" type="button" data-bs-target="#galleryCarousel" data-bs-slide="prev">
						<span class="carousel-control-prev-icon" aria-hidden="true"></span>
						<span class="visually-hidden">Previous</span>
					</button>
					<button class="carousel-control-next" type="button" data-bs-target="#galleryCarousel" data-bs-slide="next">
						<span class="carousel-control-next-icon" aria-hidden="true"></span>
						<span class="visually-hidden">Next</span>
					</button>
					<div class="carousel-indicators">${indicators}</div>
					` : ''}
				</div>
			`;

			if (carouselInstance) {
				carouselInstance.dispose();
			}

			const el = document.getElementById('galleryCarousel');
			if (el) {
				carouselInstance = new bootstrap.Carousel(el, {
					interval: 4000,
					ride: 'carousel',
				});
			}
		}

		function renderVideo(videos) {
			const root = document.getElementById('video-root');
			if (!root) return;

			if (!videos || videos.length === 0) {
				videoList = [];
				root.innerHTML = '<div class="w-100 h-100 d-flex align-items-center justify-content-center text-white"><h3 class="mb-0">Belum ada video aktif</h3></div>';
				return;
			}

			videoList = videos;
			videoIndex = 0;

			root.innerHTML = `
				<video id="tv-video-player" class="w-100 h-100" style="object-fit: contain;" autoplay muted playsinline controls preload="auto"></video>
			`;

			const player = document.getElementById('tv-video-player');
			const playCurrent = () => {
				if (!player || videoList.length === 0) return;
				const current = videoList[videoIndex];
				player.src = current.url;
				player.load();
				player.play().catch(() => {});
			};

			player?.addEventListener('ended', () => {
				videoIndex = (videoIndex + 1) % videoList.length;
				playCurrent();
			});

			player?.addEventListener('loadedmetadata', () => {
				player.play().catch(() => {});
			});

			playCurrent();
		}

		async function loadInitial() {
			const response = await fetch(`${apiBase}/${slug}`);
			if (!response.ok) {
				throw new Error('Gagal memuat data TV');
			}

			const payload = await response.json();
			const data = payload.data || {};

			document.getElementById('logo-nagari').src = data.nagari?.logo_url || '{{ asset('storage/default-avatar.png') }}';
			document.getElementById('title-nagari').textContent = ` Nagari ${data.nagari?.name || 'Sikucur'} `;
			document.getElementById('bupati-image').src = data.tv?.bupati_image_url || '{{ asset('storage/default-avatar.png') }}';
			document.getElementById('bamus-image').src = data.tv?.bamus_image_url || '{{ asset('storage/default-avatar.png') }}';
			document.getElementById('tv-name').textContent = data.tv?.name || 'TV Informasi';
			document.getElementById('running-text').textContent = data.tv?.running_text || 'Selamat datang di TV Informasi Nagari';
			document.getElementById('absensi-date').textContent = `E-Absensi ${new Date().toLocaleDateString('id-ID')}`;

			renderAbsensi(data.absensi || []);
			renderVideo(data.videos || []);
			renderGallery(data.gallery || []);
		}

		async function refreshAbsensi() {
			const response = await fetch(`${apiBase}/${slug}/absensi-hari-ini`);
			if (!response.ok) return;
			const payload = await response.json();
			renderAbsensi(payload.data || []);
		}

		async function setupRealtime() {
			const response = await fetch(`${apiBase}/${slug}/realtime`);
			if (!response.ok) return;
			const payload = await response.json();
			const config = payload.data;

			if (!config?.url || !config?.anon_key) {
				return;
			}

			const supabase = createClient(config.url, config.anon_key);
			const channelName = config.channel || 'realtime_absensi_tv';
			const channel = supabase.channel(channelName);

			(config.events || []).forEach((event) => {
				channel.on('postgres_changes', {
					event: event.event,
					schema: event.schema,
					table: event.table,
				}, () => {
					refreshAbsensi();
				});
			});

			channel.subscribe();

			realtimeCleanup = () => {
				supabase.removeChannel(channel);
			};
		}

		function startAutoScroll() {
			const container = document.getElementById('absensi-list');
			if (!container) return;

			let scrollY = 0;
			let direction = 1;

			setInterval(() => {
				const parent = container.parentElement;
				if (!parent) return;

				scrollY += direction;
				container.style.transform = `translateY(-${scrollY}px)`;

				if (scrollY >= container.scrollHeight - parent.clientHeight) {
					direction = -1;
				} else if (scrollY <= 0) {
					direction = 1;
				}
			}, 50);
		}

		document.addEventListener('DOMContentLoaded', async () => {
			try {
				await loadInitial();
				await setupRealtime();
			} catch (error) {
				const list = document.getElementById('absensi-list');
				if (list) {
					list.innerHTML = '<li class="list-group-item text-center text-danger">Gagal memuat data TV</li>';
				}
			}

			startAutoScroll();
		});

		window.addEventListener('beforeunload', () => {
			if (typeof realtimeCleanup === 'function') {
				realtimeCleanup();
			}
		});
	</script>
</body>

</html>
