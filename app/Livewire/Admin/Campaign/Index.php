<?php

namespace App\Livewire\Admin\Campaign;

use App\Models\EmailCampaign;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin.app')]
#[Title('Email Blast / Campaign')]
class Index extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $pageTitle = 'Email Blast Promosi';
    public string $search = '';
    public string $filterStatus = 'all';
    public int $perPage = 10;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function delete(string $id): void
    {
        $campaign = EmailCampaign::findOrFail($id);
        $campaign->delete();
        session()->flash('success', 'Campaign promosi berhasil dihapus.');
    }

    public function render()
    {
        $campaigns = EmailCampaign::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('title', 'like', "%{$this->search}%")
                        ->orWhere('subject', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterStatus !== 'all', function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->withCount('recipients')
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.admin.campaign.index', [
            'campaigns' => $campaigns,
        ]);
    }
}

