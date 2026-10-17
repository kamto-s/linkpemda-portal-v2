<?php

use App\Enums\ContactCategory;
use App\Livewire\Admin\Contact\Form as ContactForm;
use App\Livewire\Admin\Contact\Index as ContactIndex;
use App\Models\Contact;
use App\Models\User;
use Database\Seeders\ContactSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(ContactSeeder::class);
});

test('authenticated user can view contact list and filter by category', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(ContactIndex::class)
        ->assertOk()
        ->assertSee('Bpk. Hendra Gunawan')
        ->assertSee('Bpk. Dimas Pratama')
        ->set('selectedCategory', 'pemerintah')
        ->assertSee('Bpk. Hendra Gunawan')
        ->assertDontSee('Bpk. Dimas Pratama')
        ->set('selectedCategory', 'swasta')
        ->assertSee('Bpk. Dimas Pratama')
        ->assertDontSee('Bpk. Hendra Gunawan')
        ->call('resetFilters')
        ->set('search', 'Hendra')
        ->assertSee('Bpk. Hendra Gunawan');
});

test('user can access contact create page and create a new contact', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.contact.create'))
        ->assertOk();

    Livewire::actingAs($user)
        ->test(ContactForm::class)
        ->set('name', 'Ibu Ratna Sari')
        ->set('email', 'ratna@bappeda.go.id')
        ->set('organization_name', 'Bappeda Jawa Timur')
        ->set('position', 'Sekretaris Bappeda')
        ->set('phone', '08123456789')
        ->set('category', 'pemerintah')
        ->set('is_active', true)
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.contact.index'));

    $created = Contact::where('email', 'ratna@bappeda.go.id')->first();
    expect($created)->not->toBeNull()
        ->and($created->name)->toBe('Ibu Ratna Sari')
        ->and($created->category)->toBe(ContactCategory::Pemerintah);
});

test('user can access contact edit page and edit an existing contact', function () {
    $user = User::factory()->create();
    $contact = Contact::first();

    $this->actingAs($user)
        ->get(route('admin.contact.edit', $contact->id))
        ->assertOk();

    Livewire::actingAs($user)
        ->test(ContactForm::class, ['contact' => $contact])
        ->assertSet('name', $contact->name)
        ->set('position', 'Kepala Dinas Kominfo Jabar')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('admin.contact.index'));

    expect($contact->fresh()->position)->toBe('Kepala Dinas Kominfo Jabar');
});

test('user can toggle contact status and delete a contact', function () {
    $user = User::factory()->create();
    $contact = Contact::first();

    Livewire::actingAs($user)
        ->test(ContactIndex::class)
        ->call('toggleStatus', $contact->id);

    expect($contact->fresh()->is_active)->toBeFalse();

    Livewire::actingAs($user)
        ->test(ContactIndex::class)
        ->call('confirmDelete', $contact->id)
        ->assertDispatched('open-delete-modal')
        ->call('delete')
        ->assertDispatched('close-delete-modal');

    expect(Contact::find($contact->id))->toBeNull();
});
