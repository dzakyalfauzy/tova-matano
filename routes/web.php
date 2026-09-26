<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WebController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Public Routes based on PRD Sitemap
Route::get('/', [WebController::class, 'home'])->name('home');
Route::get('/tentang', [WebController::class, 'about'])->name('about');
Route::get('/struktur-organisasi', [WebController::class, 'organization'])->name('organization');
Route::get('/anggota', [WebController::class, 'members'])->name('members');
Route::get('/program', [WebController::class, 'programs'])->name('programs');
Route::get('/program/{slug}', [WebController::class, 'programDetail'])->name('programs.detail');
Route::get('/blog', [WebController::class, 'blog'])->name('blog');
Route::get('/blog/{slug}', [WebController::class, 'blogDetail'])->name('blog.detail');
Route::get('/galeri', [WebController::class, 'gallery'])->name('gallery');
Route::get('/gabung-donasi', [WebController::class, 'joinAndDonate'])->name('join');
Route::get('/kontak', [WebController::class, 'contact'])->name('contact');

// Breeze default routes (kept for dashboard if needed, though Filament handles admin)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
