<?php

use App\Http\Controllers\Admin\CampaignImageUploadController;
use App\Livewire\Admin\Campaign\Form as CampaignForm;
use App\Livewire\Admin\Campaign\Index as CampaignIndex;
use App\Livewire\Admin\Campaign\Show as CampaignShow;
use App\Livewire\Admin\Category\Index as CategoryIndex;
use App\Livewire\Admin\Contact\Form as ContactForm;
use App\Livewire\Admin\Contact\Index as ContactIndex;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Participant\Form as ParticipantForm;
use App\Livewire\Admin\Participant\Index as ParticipantIndex;
use App\Livewire\Admin\Role\Form as RoleForm;
use App\Livewire\Admin\Role\Index as RoleIndex;
use App\Livewire\Admin\SystemSetting\Index as SystemSettingIndex;
use App\Livewire\Admin\Template\Form as TemplateForm;
use App\Livewire\Admin\Template\Index as TemplateIndex;
use App\Livewire\Admin\Template\Show as TemplateShow;
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
