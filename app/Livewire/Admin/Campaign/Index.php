<?php

namespace App\Livewire\Admin\Campaign;

use App\Enums\CampaignStatus;
use App\Models\EmailCampaign;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin.app')]
#[Title('Campaign Email')]
class Index extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public string $pageTitle = 'Campaign Email';

    public string $search = '';

    public string $selectedStatus = '';

    public int $perPage = 10;

    // Delete confirmation modal state
    public ?string $campaignToDeleteId = null;

    public string $campaignToDeleteTitle = '';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedSelectedStatus(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'selectedStatus']);
        $this->resetPage();
    }

    public function confirmDelete(string $id): void
    {
        $campaign = EmailCampaign::findOrFail($id);
        $this->campaignToDeleteId = $campaign->id;
        $this->campaignToDeleteTitle = $campaign->title;

        $this->dispatch('open-delete-modal');
    }

    public function delete(?string $id = null): void
    {
        $targetId = $id ?: $this->campaignToDeleteId;

        if (! $targetId) {
            return;
        }

        $campaign = EmailCampaign::findOrFail($targetId);
        $title = $campaign->title;
        $campaign->delete();

        session()->flash('success', "Campaign \"{$title}\" berhasil dihapus.");

        $this->dispatch('close-delete-modal');
        $this->reset(['campaignToDeleteId', 'campaignToDeleteTitle']);
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
            ->when($this->selectedStatus, function ($query) {
                $query->where('status', $this->selectedStatus);
            })
            ->latest('created_at')
            ->paginate($this->perPage);

        return view('livewire.admin.campaign.index', [
            'campaigns' => $campaigns,
            'statuses' => CampaignStatus::cases(),
        ]);
    }
}
