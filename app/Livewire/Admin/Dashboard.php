<?php

namespace App\Livewire\Admin;

use App\Enums\CampaignStatus;
use App\Models\Contact;
use App\Models\EmailCampaign;
use App\Models\EmailTemplate;
use App\Models\Participant;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin.app')]
#[Title('Dashboard')]
class Dashboard extends Component
{
    public int $selectedYear;
    public int $selectedCampaignYear;

    public function mount(): void
    {
        abort_unless(auth()->user()?->can('view-dashboard'), 403, 'Anda tidak memiliki hak akses untuk melihat dashboard.');

        $this->selectedYear         = (int) now()->format('Y');
        $this->selectedCampaignYear = (int) now()->format('Y');
    }

    public function updatedSelectedYear(): void
    {
        $data = $this->getChartData();

        $this->dispatch('update-chart',
            labels: $data['labels'],
            totalData: $data['totalData'],
            sentData: $data['sentData'],
        );
    }

    public function updatedSelectedCampaignYear(): void
    {
        $campaignData = $this->getCampaignChartData();

        $this->dispatch('update-campaign-chart',
            labels: $campaignData['labels'],
            sentRecipients: $campaignData['sentRecipients'],
            totalRecipients: $campaignData['totalRecipients'],
        );
    }

    protected function getChartData(): array
    {
        $months = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
            7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];

        $chartLabels   = array_values($months);
        $chartData     = [];
        $chartSentData = [];

        foreach (array_keys($months) as $m) {
            $baseQuery = Participant::whereYear('created_at', $this->selectedYear)
                ->whereMonth('created_at', $m);

            $chartData[]     = (clone $baseQuery)->count();
            $chartSentData[] = (clone $baseQuery)->where('email_status', 'sent')->count();
        }

        return [
            'labels'    => $chartLabels,
            'totalData' => $chartData,
            'sentData'  => $chartSentData,
        ];
    }

    protected function getCampaignChartData(): array
    {
        $months = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
            7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
        ];

        $labels = array_values($months);
        $sentRecipients = [];
        $totalRecipients = [];

        foreach (array_keys($months) as $m) {
            $baseQuery = EmailCampaign::whereYear('created_at', $this->selectedCampaignYear)
                ->whereMonth('created_at', $m);

            $sentRecipients[]  = (int) (clone $baseQuery)->sum('sent_count');
            $totalRecipients[] = (int) (clone $baseQuery)->sum('total_recipients');
        }

        return [
            'labels'          => $labels,
            'sentRecipients'  => $sentRecipients,
            'totalRecipients' => $totalRecipients,
        ];
    }

    public function render()
    {
        // =========================================================================
        // 1. DATA ADMINISTRASI PESERTA LINKPEMDA
        // =========================================================================
        $totalParticipants = Participant::count();
        $emailSentCount    = Participant::where('email_status', 'sent')->count();
        $emailPendingCount = Participant::where(function ($q) {
            $q->where('email_status', 'pending')
              ->orWhereNull('email_status');
        })->count();
        $totalAmount       = Participant::sum('amount');

        $emailSentPercentage = $totalParticipants > 0
            ? round(($emailSentCount / $totalParticipants) * 100, 1)
            : 0;

        $chartData = $this->getChartData();

        $currentYear = (int) now()->format('Y');
        $availableYears = range($currentYear, $currentYear - 3);

        // =========================================================================
        // 2. DATA EMAIL MARKETING & PROMOSI
        // =========================================================================
        $totalContacts       = Contact::count();
        $activeContacts      = Contact::where('is_active', true)->count();
        $totalCampaigns      = EmailCampaign::count();
        $completedCampaigns  = EmailCampaign::where('status', CampaignStatus::Completed)->count();
        $totalTemplates      = EmailTemplate::count();

        // Total email blast terkirim
        $totalMarketingSent   = (int) EmailCampaign::sum('sent_count');
        $totalMarketingFailed = (int) EmailCampaign::sum('failed_count');

        $campaignChartData    = $this->getCampaignChartData();

        return view('livewire.admin.dashboard', [
            // Modul 1: Administrasi Peserta Linkpemda
            'totalParticipants'   => $totalParticipants,
            'emailSentCount'      => $emailSentCount,
            'emailPendingCount'   => $emailPendingCount,
            'totalAmount'         => $totalAmount,
            'emailSentPercentage' => $emailSentPercentage,
            'chartLabels'         => $chartData['labels'],
            'chartData'           => $chartData['totalData'],
            'chartSentData'       => $chartData['sentData'],
            'availableYears'      => $availableYears,

            // Modul 2: Email Marketing
            'totalContacts'           => $totalContacts,
            'activeContacts'          => $activeContacts,
            'totalCampaigns'          => $totalCampaigns,
            'completedCampaigns'      => $completedCampaigns,
            'totalTemplates'          => $totalTemplates,
            'totalMarketingSent'      => $totalMarketingSent,
            'totalMarketingFailed'    => $totalMarketingFailed,
            'campaignSentRecipients'  => $campaignChartData['sentRecipients'],
            'campaignTotalRecipients' => $campaignChartData['totalRecipients'],
        ]);
    }
}
