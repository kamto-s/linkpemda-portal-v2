<?php

use App\Http\Controllers\Admin\CampaignImageUploadController;
use App\Http\Controllers\Admin\InvoiceDocumentController;
use App\Http\Controllers\Admin\ParticipantDocumentController;
use App\Http\Controllers\Admin\TrenParticipantDocumentController;
use App\Livewire\Admin\Campaign\Form as CampaignForm;
use App\Livewire\Admin\Campaign\Index as CampaignIndex;
use App\Livewire\Admin\Campaign\Show as CampaignShow;
use App\Livewire\Admin\Category\Index as CategoryIndex;
use App\Livewire\Admin\Contact\Form as ContactForm;
use App\Livewire\Admin\Contact\Index as ContactIndex;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Invoice\Form as InvoiceForm;
use App\Livewire\Admin\Invoice\Index as InvoiceIndex;
use App\Livewire\Admin\Invoice\Show as InvoiceShow;
use App\Livewire\Admin\Participant\Form as ParticipantForm;
use App\Livewire\Admin\Participant\Index as ParticipantIndex;
use App\Livewire\Admin\Participant\Show as ParticipantShow;
use App\Livewire\Admin\Role\Form as RoleForm;
use App\Livewire\Admin\Role\Index as RoleIndex;
use App\Livewire\Admin\SystemSetting\Index as SystemSettingIndex;
use App\Livewire\Admin\Template\Form as TemplateForm;
use App\Livewire\Admin\Template\Index as TemplateIndex;
use App\Livewire\Admin\Template\Show as TemplateShow;
use App\Livewire\Admin\TrenParticipant\Form as TrenParticipantForm;
use App\Livewire\Admin\TrenParticipant\Index as TrenParticipantIndex;
use App\Livewire\Admin\TrenParticipant\Show as TrenParticipantShow;
use App\Livewire\Admin\User\Form as UserForm;
use App\Livewire\Admin\User\Index as UserIndex;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\SetPassword;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes (Frontend Portal)
|--------------------------------------------------------------------------
*/

Route::view('/', 'welcome')->name('home');

/*
|--------------------------------------------------------------------------
| Public Certificate Verification (Frontend)
|--------------------------------------------------------------------------
*/
// Halaman verifikasi publik (direct scan QR code baru)
Route::get('/sertifikat/cek/{certificate_number}', function (string $certificate_number) {
    $cleanNumber = trim(urldecode($certificate_number));
    $participant = \App\Models\Participant::where('certificate_number', $cleanNumber)
        ->orWhere('certificate_number', $certificate_number)
        ->first();

    return view('certificate.verify', compact('participant', 'certificate_number'));
})->name('certificate.verify')->where('certificate_number', '.*');

// Alias kompatibilitas mundur dengan sistem lama linkpemda (direct scan QR code lama)
Route::get('/sertifikat/status/{no_certf}', function (string $no_certf) {
    return redirect()->route('certificate.verify', ['certificate_number' => $no_certf]);
})->name('sertifikat.status.dinamis')->where('no_certf', '.*');

// Form pencarian manual nomor sertifikat (kompatibilitas sistem lama)
Route::get('/sertifikat', function (\Illuminate\Http\Request $request) {
    if ($no = $request->query('no_certf') ?? $request->query('certificate_number')) {
        return redirect()->route('certificate.verify', ['certificate_number' => $no]);
    }
    return view('certificate.verify', ['participant' => null, 'certificate_number' => '']);
})->name('sertifikat.search');

Route::post('/sertifikat', function (\Illuminate\Http\Request $request) {
    $no = $request->input('no_certf') ?? $request->input('certificate_number');
    if ($no) {
        return redirect()->route('certificate.verify', ['certificate_number' => trim($no)]);
    }
    return redirect()->route('sertifikat.search');
})->name('sertifikat.status');

/*
|--------------------------------------------------------------------------
| Authentication Routes (Guest Only)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/forgot-password', ForgotPassword::class)->name('password.request');
    Route::get('/set-password/{token}', SetPassword::class)->name('password.set');
    Route::get('/reset-password/{token}', SetPassword::class)->name('password.reset');
});

/*
|--------------------------------------------------------------------------
| Admin Panel Routes (Auth Protected)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Logout
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        return redirect()->route('login');
    })->name('logout');

    // Semua route Admin berada di dalam grup ini:
    Route::prefix('admin')->name('admin.')->group(function () {

        // Dashboard
        Route::get('/', Dashboard::class)->name('dashboard');
        Route::get('/dashboard', Dashboard::class);

        // CMS
        Route::get('/categories', CategoryIndex::class)->name('category.index');
        // Route::get('/articles', ArticleIndex::class)->name('article.index');
        // Route::get('/materials', MaterialIndex::class)->name('material.index');

        // Email Blast & Promosi
        Route::get('/contacts', ContactIndex::class)->name('contact.index');
        Route::get('/contacts/create', ContactForm::class)->name('contact.create');
        Route::get('/contacts/{contact}/edit', ContactForm::class)->name('contact.edit');
        Route::get('/templates', TemplateIndex::class)->name('template.index');
        Route::get('/templates/create', TemplateForm::class)->name('template.create');
        Route::get('/templates/{template}/edit', TemplateForm::class)->name('template.edit');
        Route::get('/templates/{template}', TemplateShow::class)->name('template.show');
        Route::get('/campaigns', CampaignIndex::class)->name('campaign.index');
        Route::get('/campaigns/create', CampaignForm::class)->name('campaign.create');
        Route::get('/campaigns/{campaign}/edit', CampaignForm::class)->name('campaign.edit');
        Route::get('/campaigns/{campaign}', CampaignShow::class)->name('campaign.show');
        Route::post('/campaigns/upload-image', [CampaignImageUploadController::class, 'upload'])->name('campaign.upload-image');

        // Administrasi - Data Peserta (Linkpemda)
        Route::get('/participants/linkpemda', ParticipantIndex::class)->name('participant.linkpemda.index');
        Route::get('/participants/linkpemda/create', ParticipantForm::class)->name('participant.linkpemda.create');
        Route::get('/participants/linkpemda/{participant}/edit', ParticipantForm::class)->name('participant.linkpemda.edit');
        Route::get('/participants/linkpemda/{participant}', ParticipantShow::class)->name('participant.linkpemda.show');

        // Administrasi - Invoice (Linkpemda)
        Route::get('/invoices/linkpemda', InvoiceIndex::class)->name('invoice.linkpemda.index');
        Route::get('/invoices/linkpemda/create', InvoiceForm::class)->name('invoice.linkpemda.create');
        Route::get('/invoices/linkpemda/{invoice}/edit', InvoiceForm::class)->name('invoice.linkpemda.edit');
        Route::get('/invoices/linkpemda/{invoice}', InvoiceShow::class)->name('invoice.linkpemda.show');
        Route::get('/invoices/linkpemda/{invoice}/preview', [InvoiceDocumentController::class, 'streamInline'])->name('invoice.linkpemda.preview');
        Route::get('/invoices/linkpemda/{invoice}/download', [InvoiceDocumentController::class, 'streamDownload'])->name('invoice.linkpemda.download');

        // Dokumen PDF Peserta (Sertifikat & Kwitansi) — dibuka via target="_blank"
        Route::get('/participants/linkpemda/{participant}/certificate/view', [ParticipantDocumentController::class, 'viewCertificate'])->name('participant.linkpemda.certificate.view');
        Route::get('/participants/linkpemda/{participant}/certificate/download', [ParticipantDocumentController::class, 'downloadCertificate'])->name('participant.linkpemda.certificate.download');
        Route::get('/participants/linkpemda/{participant}/certificate/print', [ParticipantDocumentController::class, 'printCertificate'])->name('participant.linkpemda.certificate.print');
        Route::get('/participants/linkpemda/{participant}/certificate/download-blank', [ParticipantDocumentController::class, 'downloadCertificateBlank'])->name('participant.linkpemda.certificate.download-blank');
        Route::get('/participants/linkpemda/{participant}/kwitansi/view', [ParticipantDocumentController::class, 'viewKwitansi'])->name('participant.linkpemda.kwitansi.view');
        Route::get('/participants/linkpemda/{participant}/kwitansi/download', [ParticipantDocumentController::class, 'downloadKwitansi'])->name('participant.linkpemda.kwitansi.download');

        // Administrasi - Data Peserta (Tren Media Diklat)
        Route::get('/participants/trendiklat', TrenParticipantIndex::class)->name('participant.trendiklat.index');
        Route::get('/participants/trendiklat/create', TrenParticipantForm::class)->name('participant.trendiklat.create');
        Route::get('/participants/trendiklat/{participant}/edit', TrenParticipantForm::class)->name('participant.trendiklat.edit');
        Route::get('/participants/trendiklat/{participant}', TrenParticipantShow::class)->name('participant.trendiklat.show');

        // Dokumen PDF Peserta Tren Media Diklat (Sertifikat & Kwitansi) — dibuka via target="_blank"
        Route::get('/participants/trendiklat/{participant}/certificate/view', [TrenParticipantDocumentController::class, 'viewCertificate'])->name('participant.trendiklat.certificate.view');
        Route::get('/participants/trendiklat/{participant}/certificate/download', [TrenParticipantDocumentController::class, 'downloadCertificate'])->name('participant.trendiklat.certificate.download');
        Route::get('/participants/trendiklat/{participant}/certificate/print', [TrenParticipantDocumentController::class, 'printCertificate'])->name('participant.trendiklat.certificate.print');
        Route::get('/participants/trendiklat/{participant}/certificate/download-blank', [TrenParticipantDocumentController::class, 'downloadCertificateBlank'])->name('participant.trendiklat.certificate.download-blank');
        Route::get('/participants/trendiklat/{participant}/kwitansi/view', [TrenParticipantDocumentController::class, 'viewKwitansi'])->name('participant.trendiklat.kwitansi.view');
        Route::get('/participants/trendiklat/{participant}/kwitansi/download', [TrenParticipantDocumentController::class, 'downloadKwitansi'])->name('participant.trendiklat.kwitansi.download');

        // Setting
        Route::get('/system-settings', SystemSettingIndex::class)->name('system-setting.index');
        Route::get('/users', UserIndex::class)->name('user.index');
        Route::get('/users/create', UserForm::class)->name('user.create');
        Route::get('/users/{user}/edit', UserForm::class)->name('user.edit');
        Route::get('/roles', RoleIndex::class)->name('role.index');
        Route::get('/roles/create', RoleForm::class)->name('role.create');
        Route::get('/roles/{role}/edit', RoleForm::class)->name('role.edit');
    });
});
