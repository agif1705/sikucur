<div>
    <div class="hero bg-base-200 min-h-screen">
        <div class="hero-content  flex-col lg:flex-col">
            <div class="indicator">
                <div class="tooltip tooltip-open tooltip-left tooltip-neutral">
                    <div class="tooltip-content">
                        <div class="animate-bounce text-orange-400 -rotate-10 text-2xl font-black">Siap!</div>
                    </div>
                </div>
                <span class="indicator-item badge badge-primary">Pak Wali</span>
                <div class="bg-base-300 grid h-32 w-32 place-items-center">
                    <div class="avatar">
                        <div class="w-full rounded">
                            <img src="{{ asset('logo/wali_nagari.png') }}" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center w-full ">
                <p class="px-6">
                    Laporan ini akan di baca oleh <span class="font-bold">Pak Wali Asrul Khairi,
                        A.Md</span> Wali Nagari Sikucur
                </p>
            </div>
            <div class="card bg-base-100 w-full max-w-md shrink-0 shadow-2xl">
                <div class="card-body">
                    <form wire:submit="save" class="flex flex-col">
                        <h5 class="mb-5 text-2xl font-bold">Lapor Pak Wali</h5>
                        <label class="fieldset-label">Tujuan Perangkat yang dilaporkan </label>
                        <select wire:model.live="tujuan" class="select select-primary w-full max-w-xs ">
                            <option value="" disabled selected>Tujuan Laporan</option>
                            @foreach ($this->jabatan as $item)
                                @if ($item->id !== 1)
                                    <option value="{{ $item->id }}">{{ Str::ucfirst($item->name) }}</option>
                                @endif
                            @endforeach
                        </select>
                        <x-landing.input-text :model="'name'" :type="'text'">name</x-landing.input-text>
                        <x-landing.input-text :model="'no_hp'" :type="'number'">Whatsapp</x-landing.input-text>
                        <x-landing.input-text :model="'coment'" :type="'textarea'">Laporan</x-landing.input-text>
                        <button class="btn btn-success mt-4">Simpan</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- <div class="hero bg-base-400 min-vh-100 p-1 lg:p-3">
        <div class="hero-content flex-col lg:flex-col">
            <div>
                <h5 class="text-3xl font-bold text-center">Lapor Pak Wali {{ $this->tujuan }}</h5>
                <p class="py-1">
                <form wire:submit="save">
                    <fieldset
                        class="fieldset w-md min-h-full bg-base-300 border border-base-300 p-1 rounded-box justify-center">
                        <legend class="fieldset-legend min-w-full"><img
                                src="https://img.daisyui.com/images/stock/photo-1635805737707-575885ab0820.webp"
                                class="max-w-sm rounded-lg shadow-2xl" /></legend>

                        <select wire:model.live="tujuan" class="select select-primary w-full max-w-xs ">
                            <option value="" disabled selected>Tujuan Laporan</option>
                            @foreach ($this->jabatan as $item)
                                @if ($item->id !== 1)
                                    <option value="{{ $item->id }}">{{ Str::ucfirst($item->name) }}</option>
                                @endif
                            @endforeach
                        </select>
                        <x-landing.input-text :model="'name'" :type="'text'">name</x-landing.input-text>
                        <x-landing.input-text :model="'no_hp'" :type="'number'">Whatsapp</x-landing.input-text>
                        <x-landing.input-text :model="'coment'" :type="'textarea'">Laporan</x-landing.input-text>
                        <button class="btn btn-primary mt-4">Login</button>
                    </fieldset>
                </form>
                </p>
            </div>
        </div>
    </div> --}}
</div>
