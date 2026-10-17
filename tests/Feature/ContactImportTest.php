<?php

use App\Enums\ContactCategory;
use App\Livewire\Admin\Contact\Index as ContactIndex;
use App\Models\Contact;
use App\Models\User;
use App\Services\ContactImportService;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

test('contact import service can generate an excel template', function () {
    $service = new ContactImportService();
    $binary = $service->generateTemplate();

    expect($binary)->not->toBeEmpty()
        ->and(strlen($binary))->toBeGreaterThan(1000);
});

test('contact import service imports new contacts and handles duplicates', function () {
    $existing = Contact::create([
        'name' => 'Existing Person',
        'email' => 'existing@pemda.go.id',
        'organization_name' => 'Bappeda Lama',
        'category' => ContactCategory::Pemerintah,
        'is_active' => true,
    ]);

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'Nama Lengkap');
    $sheet->setCellValue('B1', 'Alamat Email');
    $sheet->setCellValue('C1', 'Instansi / Lembaga');
    $sheet->setCellValue('D1', 'Jabatan');
    $sheet->setCellValue('E1', 'No Telepon');
    $sheet->setCellValue('F1', 'Kategori');
    $sheet->setCellValue('G1', 'Status');

    // Row 2: new contact
    $sheet->setCellValue('A2', 'Kontak Baru');
    $sheet->setCellValue('B2', 'baru@dinas.go.id');
    $sheet->setCellValue('C2', 'Dinas Pendidikan');
    $sheet->setCellValue('D2', 'Staff');
    $sheet->setCellValue('E2', '0811111111');
    $sheet->setCellValue('F2', 'Pemerintah');
    $sheet->setCellValue('G2', 'Aktif');

    // Row 3: duplicate contact (update)
    $sheet->setCellValue('A3', 'Existing Person Updated');
    $sheet->setCellValue('B3', 'existing@pemda.go.id');
    $sheet->setCellValue('C3', 'Bappeda Baru');
    $sheet->setCellValue('D3', 'Kabid');
    $sheet->setCellValue('E3', '0822222222');
    $sheet->setCellValue('F3', 'Swasta');
    $sheet->setCellValue('G3', 'Nonaktif');

    // Row 4: invalid email
    $sheet->setCellValue('A4', 'Invalid Email Person');
    $sheet->setCellValue('B4', 'invalid-email-format');
    $sheet->setCellValue('C4', 'Dinas');

    $tempFile = tempnam(sys_get_temp_dir(), 'test_import_') . '.xlsx';
    $writer = new Xlsx($spreadsheet);
    $writer->save($tempFile);

    $service = new ContactImportService();
    $result = $service->import($tempFile, 'update');

    expect($result['imported'])->toBe(1)
        ->and($result['updated'])->toBe(1)
        ->and(count($result['errors']))->toBe(1);

    $newContact = Contact::where('email', 'baru@dinas.go.id')->first();
    expect($newContact)->not->toBeNull()
        ->and($newContact->name)->toBe('Kontak Baru')
        ->and($newContact->category)->toBe(ContactCategory::Pemerintah)
        ->and($newContact->is_active)->toBeTrue();

    $updatedContact = $existing->fresh();
    expect($updatedContact->name)->toBe('Existing Person Updated')
        ->and($updatedContact->organization_name)->toBe('Bappeda Baru')
        ->and($updatedContact->category)->toBe(ContactCategory::Swasta)
        ->and($updatedContact->is_active)->toBeFalse();

    @unlink($tempFile);
});

test('authenticated user can upload excel file through contact index component', function () {
    $user = User::factory()->create();

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'Nama');
    $sheet->setCellValue('B1', 'Email');
    $sheet->setCellValue('C1', 'Instansi');

    $sheet->setCellValue('A2', 'Rudi Haryanto');
    $sheet->setCellValue('B2', 'rudi@diskominfo.go.id');
    $sheet->setCellValue('C2', 'Diskominfo Jabar');

    $tempFile = tempnam(sys_get_temp_dir(), 'test_lw_') . '.xlsx';
    $writer = new Xlsx($spreadsheet);
    $writer->save($tempFile);

    $file = UploadedFile::fake()->createWithContent('kontak.xlsx', (string) file_get_contents($tempFile));

    Livewire::actingAs($user)
        ->test(ContactIndex::class)
        ->call('openImportModal')
        ->assertDispatched('open-import-modal')
        ->set('importFile', $file)
        ->call('importExcel')
        ->assertHasNoErrors();

    expect(Contact::where('email', 'rudi@diskominfo.go.id')->exists())->toBeTrue();

    @unlink($tempFile);
});
