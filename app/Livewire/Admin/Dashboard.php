<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin.app')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    public function mount(): void
    {
        abort_unless(auth()->user()?->can('view-dashboard'), 403, 'Anda tidak memiliki hak akses untuk melihat dashboard.');
    }

    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}
