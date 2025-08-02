<?php

namespace App\Livewire\Homepage;

use App\Models\Coments;
use Livewire\Attributes\title;
use Livewire\Component;

// #[layout('components.layouts.home')]
#[title('Sikucur - Home')]

class HomePageLivewire extends Component
{
    public $kritik;
    public function mount()
    {
        $this->kritik = Coments::where('status', 1)->orderBy('created_at', 'desc')->get();
    }
    public function render()
    {
        return view('livewire.homepage.home-page-livewire')->layout('components.layouts.home');
    }
}
