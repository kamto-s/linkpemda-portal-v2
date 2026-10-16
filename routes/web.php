<?php

use App\Livewire\Admin\Category\Index as CategoryIndex;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Role\Form as RoleForm;
use App\Livewire\Admin\Role\Index as RoleIndex;
use App\Livewire\Admin\User\Index as UserIndex;
use App\Livewire\Auth\Login;
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

        // Setting
        Route::get('/users', UserIndex::class)->name('user.index');
        Route::get('/roles', RoleIndex::class)->name('role.index');
        Route::get('/roles/create', RoleForm::class)->name('role.create');
        Route::get('/roles/{role}/edit', RoleForm::class)->name('role.edit');
    });
});
