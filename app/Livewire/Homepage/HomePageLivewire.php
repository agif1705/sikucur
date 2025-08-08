<?php

namespace App\Livewire\Homepage;

use App\Models\Coments;
use Livewire\Component;
use Livewire\Attributes\title;
use Livewire\Attributes\Layout;

// #[layout('components.layouts.home')]
#[title('Sikucur - Home')]

class HomePageLivewire extends Component
{
    public $kritik;
    #[Layout('components.layouts.home')]
    public function render()
    {
        return view('livewire.homepage.home-page-livewire');
    }
    public function mount()
    {
        $this->kritik = Coments::where('status', 1)->orderBy('created_at', 'desc')->get();
    }
    
}
