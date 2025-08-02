<?php

namespace App\Livewire\Homepage;

use App\Models\Coments;
use App\Models\Jabatan;
use Livewire\Attributes\layout;
use Livewire\Attributes\title;
use Livewire\Component;

#[layout('components.layouts.home')]
#[title('Sikucur - Kritik dan Saran')]
class KritikPageLivewire extends Component
{
    public $jabatan, $name, $no_hp, $coment;
    public $tujuan = '';
    public function mount()
    {

        $this->jabatan = Jabatan::select('id', 'name')->get();
    }
    public function save()
    {

        Coments::create([
            'jabatan_id' => $this->tujuan,
            'name' => $this->name,
            'no_hp' => $this->no_hp,
            'coment' => $this->coment,
            'status' => 0,
        ]);

        return redirect()->to('/');
    }
    public function render()
    {
        return view('livewire.homepage.kritik-page-livewire');
    }
}
