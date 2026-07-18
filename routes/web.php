<?php

use App\Http\Controllers\AdminBookingController;
use App\Http\Controllers\AdminCmsController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PublicBookingController;
use App\Http\Controllers\PublicPagesController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicPagesController::class, 'home'])->name('home');
Route::get('/padel', [PublicPagesController::class, 'padel'])->name('padel');
Route::get('/billiard', [PublicPagesController::class, 'billiard'])->name('billiard');
Route::get('/gallery', [PublicPagesController::class, 'gallery'])->name('gallery');
Route::get('/faq', [PublicPagesController::class, 'faq'])->name('faq');
Route::get('/contact', [PublicPagesController::class, 'contact'])->name('contact');

Route::get('/booking/billiard', [PublicBookingController::class, 'create'])->name('bookings.create');
Route::post('/booking/billiard', [PublicBookingController::class, 'store'])->middleware('throttle:5,1')->name('bookings.store');
Route::post('/booking/billiard/check', [PublicBookingController::class, 'checkAvailability'])->middleware('throttle:20,1')->name('bookings.check');
Route::get('/booking/billiard/success/{bookingCode}', [PublicBookingController::class, 'success'])->name('bookings.success');
Route::get('/booking/status', [PublicBookingController::class, 'lookup'])->name('bookings.lookup');
Route::post('/booking/status', [PublicBookingController::class, 'search'])->middleware('throttle:10,1')->name('bookings.search');

Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [LoginController::class, 'create'])->name('login');
    Route::post('/admin/login', [LoginController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('/admin/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::middleware('role:admin,staff')->group(function () {
        Route::get('/admin', AdminDashboardController::class)->name('admin.dashboard');
        Route::get('/admin/bookings', [AdminBookingController::class, 'index'])->name('admin.bookings');
        Route::patch('/admin/bookings/{booking}/status', [AdminBookingController::class, 'updateStatus'])->name('admin.bookings.status');
        Route::get('/admin/schedule', [AdminBookingController::class, 'schedule'])->name('admin.schedule');
        Route::post('/admin/walk-in', [AdminBookingController::class, 'storeWalkIn'])->name('admin.walk-in');
    });

    Route::middleware('role:admin')->group(function () {
        Route::get('/admin/gallery', [AdminCmsController::class, 'galleryIndex'])->name('admin.gallery');
        Route::post('/admin/gallery', [AdminCmsController::class, 'galleryStore'])->name('admin.gallery.store');
        Route::patch('/admin/gallery/{gallery}', [AdminCmsController::class, 'galleryUpdate'])->name('admin.gallery.update');
        Route::delete('/admin/gallery/{gallery}', [AdminCmsController::class, 'galleryDestroy'])->name('admin.gallery.destroy');
        Route::get('/admin/faqs', [AdminCmsController::class, 'faqIndex'])->name('admin.faqs');
        Route::post('/admin/faqs', [AdminCmsController::class, 'faqStore'])->name('admin.faqs.store');
        Route::patch('/admin/faqs/{faq}', [AdminCmsController::class, 'faqUpdate'])->name('admin.faqs.update');
        Route::delete('/admin/faqs/{faq}', [AdminCmsController::class, 'faqDestroy'])->name('admin.faqs.destroy');
        Route::get('/admin/settings', [AdminCmsController::class, 'settingsIndex'])->name('admin.settings');
        Route::patch('/admin/settings', [AdminCmsController::class, 'settingsUpdate'])->name('admin.settings.update');
    });
});
