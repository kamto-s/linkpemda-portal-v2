<?php

use App\Enums\EmailTemplateCategory;
use App\Livewire\Admin\Campaign\Form as CampaignForm;
use App\Livewire\Admin\Template\Form as TemplateForm;
use App\Livewire\Admin\Template\Index as TemplateIndex;
use App\Livewire\Admin\Template\Show as TemplateShow;
use App\Models\EmailTemplate;
use App\Models\User;
use Database\Seeders\EmailTemplateSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(EmailTemplateSeeder::class);
});

test('email templates are properly seeded with categories', function () {
    expect(EmailTemplate::count())->toBe(9);

    $marketingTemplates = EmailTemplate::byCategory(EmailTemplateCategory::Marketing)->get();
    expect($marketingTemplates)->toHaveCount(3);

    $infoTemplates = EmailTemplate::byCategory(EmailTemplateCategory::Info)->get();
    expect($infoTemplates)->toHaveCount(2);

    $agendaTemplates = EmailTemplate::byCategory(EmailTemplateCategory::Agenda)->get();
    expect($agendaTemplates)->toHaveCount(2);

    $reminderTemplates = EmailTemplate::byCategory(EmailTemplateCategory::Reminder)->get();
    expect($reminderTemplates)->toHaveCount(1);

    $formalTemplates = EmailTemplate::byCategory(EmailTemplateCategory::Formal)->get();
    expect($formalTemplates)->toHaveCount(1);
});

test('authenticated user can view template list and navigation links to separate pages', function () {
    $user = User::factory()->create();

    $tpl = EmailTemplate::first();

    Livewire::actingAs($user)
        ->test(TemplateIndex::class)
        ->assertOk()
        ->assertSee('Kelas Bimtek Unggulan & Praktis')
        ->assertSee(route('admin.template.create'))
        ->assertSee(route('admin.template.show', $tpl->id))
        ->assertSee(route('admin.template.edit', $tpl->id))
        ->set('selectedCategory', EmailTemplateCategory::Marketing->value)
        ->assertSee('Kelas Bimtek Unggulan & Praktis')
        ->assertDontSee('Tanya-Jawab Santai Seputar Bimtek')
        ->set('search', 'Early Bird')
        ->assertSee('Promo Early Bird & Kuota Rombongan')
        ->assertDontSee('In-House Training');
});

test('user can open create page and save a custom email template', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.template.create'))
        ->assertOk()
        ->assertSee('Tambah Template Email Baru');

    Livewire::actingAs($user)
        ->test(TemplateForm::class)
        ->set('name', 'Bimtek Tata Kelola Keuangan')
        ->set('category', EmailTemplateCategory::Marketing->value)
        ->set('subject', 'Undangan Kelas Spesial Keuangan Daerah')
        ->set('body_content', '<p>Halo {nama}, berikut info kelas bimtek...</p>')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.template.index'));

    $created = EmailTemplate::where('name', 'Bimtek Tata Kelola Keuangan')->first();
    expect($created)->not->toBeNull()
        ->and($created->category)->toBe(EmailTemplateCategory::Marketing)
        ->and($created->is_system)->toBeFalse();
});

test('user can open edit page and update a template', function () {
    $user = User::factory()->create();
    $tpl = EmailTemplate::first();

    $this->actingAs($user)
        ->get(route('admin.template.edit', $tpl->id))
        ->assertOk()
        ->assertSee('Ubah Template: ' . $tpl->name);

    Livewire::actingAs($user)
        ->test(TemplateForm::class, ['template' => $tpl])
        ->assertSet('isEditing', true)
        ->assertSet('name', $tpl->name)
        ->set('subject', 'Subjek Baru yang Telah Diperbarui')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.template.index'));

    expect($tpl->fresh()->subject)->toBe('Subjek Baru yang Telah Diperbarui');
});

test('user can open dedicated show page and view template details with email preview', function () {
    $user = User::factory()->create();
    $tpl = EmailTemplate::first();

    $this->actingAs($user)
        ->get(route('admin.template.show', $tpl->id))
        ->assertOk()
        ->assertSee($tpl->name)
        ->assertSee(route('admin.template.edit', $tpl->id));

    Livewire::actingAs($user)
        ->test(TemplateShow::class, ['template' => $tpl])
        ->assertOk()
        ->assertSeeHtml('iframe')
        ->assertSee($tpl->subject)
        ->call('setDevice', 'mobile')
        ->assertSet('previewDevice', 'mobile')
        ->call('toggleActive')
        ->assertHasNoErrors();
});

test('user cannot delete a system template but can delete custom template', function () {
    $user = User::factory()->create();

    $systemTpl = EmailTemplate::where('is_system', true)->first();
    expect($systemTpl)->not->toBeNull();

    Livewire::actingAs($user)
        ->test(TemplateIndex::class)
        ->call('delete', $systemTpl->id)
        ->assertSee('Template bawaan sistem tidak dapat dihapus');

    expect(EmailTemplate::find($systemTpl->id))->not->toBeNull();

    // Create custom template
    $custom = EmailTemplate::create([
        'name' => 'Custom Promo',
        'category' => EmailTemplateCategory::Marketing->value,
        'subject' => 'Subjek Promo',
        'body_content' => '<p>Konten</p>',
        'is_system' => false,
    ]);

    Livewire::actingAs($user)
        ->test(TemplateIndex::class)
        ->call('delete', $custom->id);

    expect(EmailTemplate::find($custom->id))->toBeNull();
});

test('campaign form dynamically loads templates from database and supports prefill from template query param', function () {
    $user = User::factory()->create();
    $tpl = EmailTemplate::first();

    Livewire::actingAs($user)
        ->test(CampaignForm::class)
        ->assertOk()
        ->assertViewHas('emailTemplates', function ($templates) {
            return $templates->count() === 9;
        })
        ->assertViewHas('templateCategories');

    // Test with template query param
    $this->actingAs($user)
        ->get(route('admin.campaign.create', ['template' => $tpl->id]))
        ->assertOk()
        ->assertSee($tpl->name);
});
