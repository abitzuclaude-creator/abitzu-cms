<?php

use App\Http\Controllers\AlertController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\CollectionsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InteractionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WhatsAppController;
use Illuminate\Support\Facades\Route;

// ---- Guest auth (login + password reset; no public registration per PRD) ----
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

// ---- Authenticated app ----
Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // Re-confirm password for sensitive actions (Laravel standard)
    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::get('/', fn () => redirect()->route('dashboard'))->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile / settings (PRD: /settings/profile)
    Route::get('/settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/settings/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Collections (kanban) — main screen
    Route::get('/collections', [CollectionsController::class, 'index'])->name('collections.index');
    Route::patch('/api/collections/{pi}/stage', [CollectionsController::class, 'updateStage'])->name('api.collections.stage');
    Route::patch('/api/collections/{pi}/assignee', [CollectionsController::class, 'updateAssignee'])->name('api.collections.assignee');
    Route::patch('/api/collections/{pi}/promise', [CollectionsController::class, 'updatePromise'])->name('api.collections.promise');

    // Payments
    Route::post('/api/payments', [PaymentController::class, 'store'])->name('api.payments.store');
    Route::get('/api/bank-accounts', [PaymentController::class, 'bankAccounts'])->name('api.bank-accounts');
    Route::get('/api/payments/proformas/{clientId}', [PaymentController::class, 'clientProformas'])->name('api.payments.proformas');

    // Interactions
    Route::post('/api/interactions', [InteractionController::class, 'store'])->name('api.interactions.store');

    // WhatsApp
    Route::get('/api/whatsapp/compose/{pi}', [WhatsAppController::class, 'compose'])->name('api.whatsapp.compose');
    Route::post('/api/whatsapp/log', [WhatsAppController::class, 'log'])->name('api.whatsapp.log');

    // Alerts
    Route::get('/alerts', [AlertController::class, 'index'])->name('alerts.index');
    Route::get('/api/alerts/count', [AlertController::class, 'count'])->name('api.alerts.count');
    Route::put('/api/alerts/{alert}', [AlertController::class, 'update'])->name('api.alerts.update');
});
