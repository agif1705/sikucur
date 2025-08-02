<?php

namespace App\Livewire\Homepage;

use Livewire\Component;
use Livewire\Attributes\layout;

#[layout('components.layouts.home')]
#[\Livewire\Attributes\title('Sikucur - Kegiatan')]

class KegiatanPageLivewire extends Component
{
    public function render()
    {
        return view('livewire.homepage.kegiatan-page-livewire');
    }
}
