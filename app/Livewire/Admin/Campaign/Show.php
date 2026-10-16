<?php

namespace App\Livewire\Admin\Campaign;

use App\Enums\CampaignStatus;
use App\Enums\RecipientStatus;
use App\Jobs\SendCampaignEmailJob;
use App\Models\EmailCampaign;
use App\Models\EmailCampaignRecipient;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin.app')]
#[Title('Monitoring Email Blast')]
class Show extends Component
{
    use WithPagination;

    protected string $paginationTheme = 'bootstrap';

    public EmailCampaign $campaign;
    public string $filterRecipientStatus = 'all';
    public string $searchRecipient = '';
    public int $perPage = 15;

    public function mount(EmailCampaign $campaign): void
    {
        $this->campaign = $campaign;
    }

    public function updatedFilterRecipientStatus(): void
    {
        $this->resetPage();
    }

    public function updatedSearchRecipient(): void
    {
        $this->resetPage();
    }

    public function retryFailed(): void
    {
        $failedRecipients = $this->campaign->recipients()
            ->where('status', RecipientStatus::Failed->value)
            ->get();

        if ($failedRecipients->isEmpty()) {
            session()->flash('warning', 'Tidak ada pengiriman email yang gagal.');
            return;
        }

        foreach ($failedRecipients as $recipient) {
            $recipient->update([
                'status' => RecipientStatus::Pending,
                'error_message' => null,
            ]);
            SendCampaignEmailJob::dispatch($recipient);
        }

        $this->campaign->update([
            'status' => CampaignStatus::Queued,
        ]);

        session()->flash('success', "Berhasil menjadwalkan ulang pengiriman untuk {$failedRecipients->count()} email yang gagal.");
    }

    public function render()
    {
        $this->campaign->refresh();

        $recipients = $this->campaign->recipients()
            ->with('contact')
            ->when($this->filterRecipientStatus !== 'all', function ($q) {
                $q->where('status', $this->filterRecipientStatus);
            })
            ->when($this->searchRecipient, function ($q) {
                $q->whereHas('contact', function ($sub) {
                    $sub->where('name', 'like', "%{$this->searchRecipient}%")
                        ->orWhere('email', 'like', "%{$this->searchRecipient}%")
                        ->orWhere('organization_name', 'like', "%{$this->searchRecipient}%");
                });
            })
            ->latest('updated_at')
            ->paginate($this->perPage);

        $pendingCount = $this->campaign->recipients()->where('status', RecipientStatus::Pending->value)->count();

        return view('livewire.admin.campaign.show', [
            'recipients' => $recipients,
            'pendingCount' => $pendingCount,
        ]);
    }
}

