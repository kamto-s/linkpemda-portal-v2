<?php

use App\Enums\CampaignStatus;
use App\Livewire\Admin\Campaign\Index as CampaignIndex;
use App\Models\EmailCampaign;
use App\Models\User;
use Livewire\Livewire;

test('authenticated user can view campaign list, search, and filter by status', function () {
    $user = User::factory()->create();

    $campaign1 = EmailCampaign::create([
        'title' => 'Sosialisasi SIPD 2026',
        'subject' => 'Undangan Sosialisasi',
        'body_content' => '<p>Undangan resmi</p>',
        'status' => CampaignStatus::Draft,
        'total_recipients' => 50,
    ]);

    $campaign2 = EmailCampaign::create([
        'title' => 'Bimtek Keuangan Daerah',
        'subject' => 'Jadwal Bimtek',
        'body_content' => '<p>Materi bimtek</p>',
        'status' => CampaignStatus::Completed,
        'total_recipients' => 120,
    ]);

    Livewire::actingAs($user)
        ->test(CampaignIndex::class)
        ->assertOk()
        ->assertSee('Sosialisasi SIPD 2026')
        ->assertSee('Bimtek Keuangan Daerah')
        ->set('selectedStatus', 'draft')
        ->assertSee('Sosialisasi SIPD 2026')
        ->assertDontSee('Bimtek Keuangan Daerah')
        ->call('resetFilters')
        ->set('search', 'Bimtek')
        ->assertSee('Bimtek Keuangan Daerah')
        ->assertDontSee('Sosialisasi SIPD 2026');
});

test('user can confirm and delete a campaign', function () {
    $user = User::factory()->create();

    $campaign = EmailCampaign::create([
        'title' => 'Campaign to Delete',
        'subject' => 'Subject',
        'body_content' => '<p>Content</p>',
        'status' => CampaignStatus::Draft,
    ]);

    Livewire::actingAs($user)
        ->test(CampaignIndex::class)
        ->call('confirmDelete', $campaign->id)
        ->assertDispatched('open-delete-modal')
        ->assertSet('campaignToDeleteId', $campaign->id)
        ->assertSet('campaignToDeleteTitle', 'Campaign to Delete')
        ->call('delete')
        ->assertDispatched('close-delete-modal')
        ->assertSet('campaignToDeleteId', null);

    expect(EmailCampaign::find($campaign->id))->toBeNull();
});

test('authenticated user can view campaign show page with duration and recipient table', function () {
    $user = User::factory()->create();

    $campaign = EmailCampaign::create([
        'title' => 'Monitoring Test',
        'subject' => 'Subject Test',
        'body_content' => '<p>Content</p>',
        'status' => CampaignStatus::Completed,
        'total_recipients' => 1,
        'sent_count' => 1,
        'failed_count' => 0,
        'sent_at' => now()->addSeconds(45),
    ]);

    $contact = \App\Models\Contact::create([
        'name' => 'Bpk. Hendra',
        'email' => 'hendra@example.com',
        'organization_name' => 'Dinas Kominfo',
        'category' => \App\Enums\ContactCategory::Pemerintah,
        'is_active' => true,
    ]);

    \App\Models\EmailCampaignRecipient::create([
        'campaign_id' => $campaign->id,
        'contact_id' => $contact->id,
        'status' => \App\Enums\RecipientStatus::Sent,
        'sent_at' => now(),
    ]);

    Livewire::actingAs($user)
        ->test(\App\Livewire\Admin\Campaign\Show::class, ['campaign' => $campaign])
        ->assertOk()
        ->assertSee('Monitoring Email Blast')
        ->assertSee('Durasi:')
        ->assertSee('Daftar Status Penerima')
        ->assertSee('Bpk. Hendra');

    expect($campaign->delivery_duration)->not->toBeNull();
});

