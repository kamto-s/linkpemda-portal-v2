<?php

namespace App\Livewire\Admin\Category;

use App\Models\Category;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin.app')]
#[Title('Kategori')]
class Index extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $pageTitle = 'Kategori';

    public string $search = '';

    public int $perPage = 10;

    public function updateSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $categories = Category::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', "%{$this->search}")
                    ->orWhere('slug', 'like', "%{$this->search}");
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.admin.category.index', [
            'categories' => $categories,
        ]);
    }
}
