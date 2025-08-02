<div class="navbar bg-base-100 shadow-sm">
    <div class="navbar-start">
        <div class="dropdown">
            <div tabindex="0" role="button" class="btn btn-ghost lg:hidden">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h8m-8 6h16" />
                </svg>
            </div>
            {{-- mobile display --}}
            <ul tabindex="0" class="menu menu-sm dropdown-content bg-base-100 rounded-box z-1 mt-3 w-52 p-2 shadow">

                <x-landing.nav-link :active="request()->routeIs('home')" :href="route('home')">home</x-landing.nav-link>
                <x-landing.nav-link :active="request()->routeIs('kegiatan')" :href="route('kegiatan')">kegiatan</x-landing.nav-link>
                <x-landing.nav-link :active="request()->routeIs('kritik')" :href="route('kritik')">lapor pak wali</x-landing.nav-link>
                <x-landing.nav-link :active="request()->routeIs('agenda')" :href="route('agenda')">agenda</x-landing.nav-link>
            </ul>
            <a class="btn btn-ghost text-xl" href="/">Sikucur Bangkit</a>
        </div>
    </div>
    <div class="navbar-center hidden lg:flex">
        <ul class="menu menu-horizontal px-1">
            {{-- desktop display --}}
            <x-landing.nav-link :active="request()->routeIs('home')" :href="route('home')">home</x-landing.nav-link>
            <x-landing.nav-link :active="request()->routeIs('kegiatan')" :href="route('kegiatan')">kegiatan</x-landing.nav-link>
            <x-landing.nav-link :active="request()->routeIs('kritik')" :href="route('kritik')">lapor pak wali</x-landing.nav-link>
            <x-landing.nav-link :active="request()->routeIs('agenda')" :href="route('agenda')">agenda</x-landing.nav-link>


        </ul>
    </div>
    <div class="navbar-end">
        <label class="flex cursor-pointer gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="5" />
                <path
                    d="M12 1v2M12 21v2M4.2 4.2l1.4 1.4M18.4 18.4l1.4 1.4M1 12h2M21 12h2M4.2 19.8l1.4-1.4M18.4 5.6l1.4-1.4" />
            </svg>
            <input type="checkbox" value="synthwave" class="toggle theme-controller" />
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
            </svg>
        </label>
    </div>
</div>
