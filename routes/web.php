<?php

use App\Livewire\Admin\Category\Index as CategoryIndex;
use App\Livewire\Admin\Dashboard;
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

        // CMS - Categories (URL: /admin/categories | Route Name: admin.category.index)
        Route::get('/categories', CategoryIndex::class)->name('category.index');

        // Nanti route Artikel & Materi tinggal dimasukkan di sini:
        // Route::get('/articles', ArticleIndex::class)->name('article.index');
        // Route::get('/materials', MaterialIndex::class)->name('material.index');

    });
});
