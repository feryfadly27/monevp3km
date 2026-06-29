<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\DosenRegisterController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\IdentityPasswordResetController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    // Register publik dinonaktifkan — user dibuat oleh admin via /users/create
    // Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    // Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    // Registrasi mandiri dosen (status pending, perlu aktivasi admin)
    Route::get('register/dosen',  [DosenRegisterController::class, 'create'])->name('register.dosen');
    Route::post('register/dosen', [DosenRegisterController::class, 'store'])->name('register.dosen.store');

    // Reset password via verifikasi identitas NIP/NIDN (tanpa email)
    Route::get('forgot-password',  [IdentityPasswordResetController::class, 'requestForm'])->name('password.request');
    Route::post('forgot-password', [IdentityPasswordResetController::class, 'requestSubmit'])->name('password.email');

    Route::get('verify-identity',  [IdentityPasswordResetController::class, 'verifyForm'])->name('password.identity.verify');
    Route::post('verify-identity', [IdentityPasswordResetController::class, 'verifySubmit'])->name('password.identity.verify.post');

    Route::get('reset-password-identity',  [IdentityPasswordResetController::class, 'resetForm'])->name('password.identity.reset');
    Route::post('reset-password-identity', [IdentityPasswordResetController::class, 'resetSubmit'])->name('password.identity.reset.post');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
