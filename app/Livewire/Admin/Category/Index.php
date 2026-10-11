<?php

namespace App\Livewire\Admin\Category;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.admin.app')]
#[Title('Manajemen Kategori')]
class Index extends Component
{
    public function render()
    {
        return view('livewire.admin.category.index');
    }
}
