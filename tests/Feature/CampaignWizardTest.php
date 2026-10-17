<?php

use App\Enums\CampaignStatus;
use App\Enums\ContactCategory;
use App\Livewire\Admin\Campaign\Form as CampaignForm;
use App\Models\Contact;
use App\Models\EmailCampaign;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Livewire\Livewire;

test('authenticated user can create campaign through wizard and send blast', function () {
    Queue::fake();

    $user = User::factory()->create();
    $contact = Contact::create([
        'name' => 'Bpk. Ahmad Fauzi',
        'email' => 'ahmad@example.com',
        'organization_name' => 'Dinas Pendidikan',
        'category' => ContactCategory::Pemerintah,
        'is_active' => true,
    ]);

    Livewire::actingAs($user)
        ->test(CampaignForm::class)
        ->set('title', 'Sosialisasi Baru')
        ->set('subject', 'Undangan Kerjasama')
        ->set('body_content', '<p>Halo Bapak {nama}</p>')
        ->call('goToStep2')
        ->assertSet('currentStep', 2)
        ->set('selectedContacts', [$contact->id])
        ->call('goToStep3')
        ->assertSet('currentStep', 3)
        ->call('sendBlast')
        ->assertHasNoErrors()
        ->assertRedirect();

    $campaign = EmailCampaign::latest()->first();
    expect($campaign)->not->toBeNull()
        ->and($campaign->title)->toBe('Sosialisasi Baru')
        ->and($campaign->status)->toBe(CampaignStatus::Queued)
        ->and($campaign->total_recipients)->toBe(1);
});

test('user can save draft from wizard', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(CampaignForm::class)
        ->set('title', 'Draft Campaign')
        ->set('subject', 'Subjek Draft')
        ->set('body_content', '<p>Isi Draft</p>')
        ->call('saveDraft')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.campaign.index'));

    $campaign = EmailCampaign::where('title', 'Draft Campaign')->first();
    expect($campaign)->not->toBeNull()
        ->and($campaign->status)->toBe(CampaignStatus::Draft);
});
