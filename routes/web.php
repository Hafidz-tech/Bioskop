<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\FilmController;
use App\Http\Controllers\Admin\JadwalController as AdminJadwalController;
use App\Http\Controllers\Admin\StudioController;
use App\Http\Controllers\Admin\PemesananController as AdminPemesananController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\GenreController;
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\JadwalController as UserJadwalController;
use App\Http\Controllers\User\PemesananController as UserPemesananController;
use App\Http\Controllers\User\FilmController as UserFilmController;
use App\Http\Controllers\Admin\KursiController;

// Redirect default
Route::get('/', fn() => redirect('/home'));

Auth::routes();

// Redirect home berdasarkan role
Route::get('/home', function () {
    if (auth()->user()->role === 'admin') {
        return redirect()->route('admin.dashboard');
    } else {
        return redirect()->route('user.dashboard');
    }
})->middleware('auth');


// ==================== ADMIN ROUTES ====================
Route::middleware(['auth', 'admin'])->prefix('admin')->as('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('film', FilmController::class);
    Route::resource('jadwal', AdminJadwalController::class);
    Route::resource('studio', StudioController::class);
    Route::resource('kursi', KursiController::class)->names('kursi');
    Route::resource('genre', GenreController::class);
    Route::get('/pemesanan', [AdminPemesananController::class, 'index'])->name('pemesanan.index');
    // Update status dengan 2 parameter: pemesanan dan status
    Route::put('/pemesanan/{pemesanan}/{status}/update-status', [AdminPemesananController::class, 'updateStatus'])->name('pemesanan.updateStatus');

    Route::post('studio/{studio}/bookSeats', [StudioController::class, 'bookSeats'])->name('studio.bookSeats');
});

// ==================== USER ROUTES ====================
Route::middleware(['auth'])->prefix('user')->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');

    // Lihat Jadwal Film
    Route::get('/jadwal', [UserJadwalController::class, 'index'])->name('user.jadwal.index');
    Route::get('/jadwal/{id}', [UserJadwalController::class, 'show'])->name('user.jadwal.show');

    // Proses Pemesanan
    Route::post('/pesan', [UserPemesananController::class, 'store'])->name('user.pemesanan.store');

    // Riwayat Pemesanan & Form upload bukti pembayaran
    Route::get('/pemesanan', [UserPemesananController::class, 'index'])->name('user.pemesanan.index');

    Route::post('/pemesanan', [UserPemesananController::class, 'store'])->name('user.pemesanan.store');

    // Lihat detail film
    Route::get('film/{id}', [UserFilmController::class, 'show'])->name('user.film.show');

    Route::post('/pemesanan/{pemesanan}/payment', [UserPemesananController::class, 'storePayment'])->name('user.pemesanan.payment');
    
});