<div>
    <div class="carousel w-full min-h-full">
        <div id="slide1" class="carousel-item relative w-full">
            <div class="hero min-h-9/10"
                style="background-image: url(https://img.daisyui.com/images/stock/photo-1507358522600-9f71e620c44e.webp);">
                <div class="hero-overlay"></div>
                <div class="hero-content text-neutral-content text-center">
                    <div class="max-w-md">
                        <h1 class="mb-5 text-5xl font-bold">Selamat Datang Di Nagari Sikucur</h1>
                        <p class="mb-5">
                            Provident cupiditate voluptatem et in. Quaerat fugiat ut assumenda excepturi exercitationem
                            quasi. In deleniti eaque aut repudiandae et a id nisi.
                        </p>
                        <button class="btn btn-primary">Get Started</button>
                    </div>
                    <div class="absolute left-5 right-5 top-1/2 flex -translate-y-1/2 transform justify-between">
                        <a href="#slide4" class="btn btn-circle">❮</a>
                        <a href="#slide2" class="btn btn-circle">❯</a>
                    </div>
                </div>
            </div>
        </div>
        <div id="slide2" class="carousel-item relative w-full">
            <img src="https://img.daisyui.com/images/stock/photo-1609621838510-5ad474b7d25d.webp" class="w-full" />
            <div class="absolute left-5 right-5 top-1/2 flex -translate-y-1/2 transform justify-between">
                <a href="#slide1" class="btn btn-circle">❮</a>
                <a href="#slide3" class="btn btn-circle">❯</a>
            </div>
        </div>
        <div id="slide3" class="carousel-item relative w-full">
            <img src="https://img.daisyui.com/images/stock/photo-1414694762283-acccc27bca85.webp" class="w-full" />
            <div class="absolute left-5 right-5 top-1/2 flex -translate-y-1/2 transform justify-between">
                <a href="#slide2" class="btn btn-circle">❮</a>
                <a href="#slide4" class="btn btn-circle">❯</a>
            </div>
        </div>
        <div id="slide4" class="carousel-item relative w-full">
            <img src="https://img.daisyui.com/images/stock/photo-1665553365602-b2fb8e5d1707.webp" class="w-full" />
            <div class="absolute left-5 right-5 top-1/2 flex -translate-y-1/2 transform justify-between">
                <a href="#slide3" class="btn btn-circle">❮</a>
                <a href="#slide1" class="btn btn-circle">❯</a>
            </div>
        </div>
    </div>


    <div class="bg-base-100 p-10">
        <h1 class="text-5xl font-bold text-center mb-6">Kritik Dan Saran</h1>
        <div class="flex-wrap flex items-center justify-center gap-4 text-center sm:flex">
            @foreach ($this->kritik as $item)
                <div
                    class="w-full p-4 mb-6 rounded-lg shadow bg-base-200 lg:w-80 lg:h-auto to-base-600 sm:inline-block">
                    <div class="flex items-start text-left">
                        <div class="ml-1">
                            <p class="flex flex-end items-baseline">
                                <span class="text-2xl font-bold">
                                    {{ $item->name }}
                                </span>
                            </p>
                            <span class="font-bold">
                                {{ $item->no_hp }}
                            </span>
                            <div class="items-end">
                                <p class="font-semibold">
                                    {{ $item->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-3 h-50 text-left bg-base-300 rounded p-4">
                        <p class="max-w-xs mt-1 font-semibold text-base-content">
                            {{ $item->coment }}
                        </p>
                    </div>
                    <div class="flex flex-end mt-6 text-info">
                        <span class="font-bold text-base-content">
                            {{ $item->jabatan->name }}
                        </span>
                    </div>

                </div>
            @endforeach

        </div>
    </div>
</div>
