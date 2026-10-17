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

test('step 2 bulk selection and filtering works properly', function () {
    $user = User::factory()->create();

    $pemda1 = Contact::create([
        'name' => 'Bpk. Budi Santoso',
        'email' => 'budi@pemda.go.id',
        'organization_name' => 'Bappeda',
        'category' => ContactCategory::Pemerintah,
        'is_active' => true,
    ]);

    $pemda2 = Contact::create([
        'name' => 'Ibu Siti Aminah',
        'email' => 'siti@pemda.go.id',
        'organization_name' => 'Disdik',
        'category' => ContactCategory::Pemerintah,
        'is_active' => true,
    ]);

    $swasta1 = Contact::create([
        'name' => 'Bpk. Charlie Wijaya',
        'email' => 'charlie@company.com',
        'organization_name' => 'PT Maju Bersama',
        'category' => ContactCategory::Swasta,
        'is_active' => true,
    ]);

    $component = Livewire::actingAs($user)
        ->test(CampaignForm::class)
        ->set('currentStep', 2)
        ->assertSee('Target Penerima');

    // Test selectAll pemerintah
    $component->call('selectAll', 'pemerintah')
        ->assertSet('selectedContacts', [$pemda1->id, $pemda2->id]);

    // Test deselectAll
    $component->call('deselectAll', 'all')
        ->assertSet('selectedContacts', []);

    // Test selectAll swasta
    $component->call('selectAll', 'swasta')
        ->assertSet('selectedContacts', [$swasta1->id]);

    // Test selectAll all
    $component->call('selectAll', 'all');
    $selected = $component->get('selectedContacts');
    expect($selected)->toContain($pemda1->id, $pemda2->id, $swasta1->id);

    // Test toggleSelectVisible unselects visible
    $component->call('toggleSelectVisible')
        ->assertSet('selectedContacts', [])
        ->assertSet('selectAll', false);

    // Test toggleSelectVisible selects visible
    $component->call('toggleSelectVisible')
        ->assertSet('selectAll', true);
    $newSelected = $component->get('selectedContacts');
    expect($newSelected)->toContain($pemda1->id, $pemda2->id, $swasta1->id);

    // Test wire:model selectAll=false
    $component->set('selectAll', false)
        ->assertSet('selectedContacts', [])
        ->assertSet('selectAll', false);

    // Test wire:model selectAll=true
    $component->set('selectAll', true)
        ->assertSet('selectAll', true);
    expect($component->get('selectedContacts'))->toContain($pemda1->id, $pemda2->id, $swasta1->id);

    // Test unselecting one contact updates selectAll to false
    $component->set('selectedContacts', [$pemda1->id])
        ->assertSet('selectAll', false);
});
