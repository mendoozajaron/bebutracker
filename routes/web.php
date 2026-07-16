<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard;


Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', Dashboard::class)->name('dashboard');
});

require __DIR__.'/settings.php';
