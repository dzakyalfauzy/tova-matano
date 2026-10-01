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
Route::get('/partnership', [WebController::class, 'partnerships'])->name('partnerships');
Route::get('/program', [WebController::class, 'programs'])->name('programs');
Route::get('/program/{slug}', [WebController::class, 'programCategory'])->name('programs.category');
Route::get('/program/{program_slug}/{activity_slug}', [WebController::class, 'programDetail'])->name('programs.detail');
Route::get('/blog', [WebController::class, 'blog'])->name('blog');
Route::get('/blog/{slug}', [WebController::class, 'blogDetail'])->name('blog.detail');
Route::get('/galeri', [WebController::class, 'gallery'])->name('gallery');
Route::get('/kualitas-air', [WebController::class, 'waterQuality'])->name('waterQuality');
Route::get('/gabung-donasi', [WebController::class, 'joinAndDonate'])->name('join');
Route::get('/kontak', [WebController::class, 'contact'])->name('contact');

// Breeze default routes (kept for dashboard if needed, though Filament handles admin)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::post('/gabung-donasi/daftar', [WebController::class, 'submitVolunteer'])->name('volunteer.submit');
Route::post('/kontak/kirim', [WebController::class, 'submitContact'])->name('contact.submit');

require __DIR__.'/auth.php';
