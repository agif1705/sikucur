<?php

namespace App\Livewire\Homepage;

use Livewire\Attributes\layout;
use Livewire\Attributes\title;
use Livewire\Attributes\Theme;
use Livewire\Component;

#[layout('components.layouts.home')]
#[title('Sikucur - Agenda')]
class AgendaPageLivewire extends Component
{
    public function render()
    {
        return view('livewire.homepage.agenda-page-livewire');
    }
}
