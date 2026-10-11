<?php

namespace App\Livewire\Admin;

use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Layout('layouts.admin.app')]
#[Title('Dashboard')]
class Dashboard extends Component
{
  public function render()
  {
    return view('livewire.admin.dashboard');
  }
}
