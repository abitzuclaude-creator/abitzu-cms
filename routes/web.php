<?php

use App\Http\Controllers\AlertController;
use App\Http\Controllers\CollectionsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InteractionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\WhatsAppController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/', fn() => redirect()->route('dashboard'))->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

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
