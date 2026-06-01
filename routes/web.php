<?php

use App\Http\Controllers\AgentController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CollectionsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InteractionController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProformaController;
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

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);
    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::get('/', fn () => redirect()->route('dashboard'))->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/settings/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/settings/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Collections (kanban)
    Route::get('/collections', [CollectionsController::class, 'index'])->name('collections.index');
    Route::patch('/api/collections/{pi}/stage', [CollectionsController::class, 'updateStage'])->name('api.collections.stage');
    Route::patch('/api/collections/{pi}/assignee', [CollectionsController::class, 'updateAssignee'])->name('api.collections.assignee');
    Route::patch('/api/collections/{pi}/promise', [CollectionsController::class, 'updatePromise'])->name('api.collections.promise');

    // Clients CRUD
    Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
    Route::get('/clients/create', [ClientController::class, 'create'])->name('clients.create');
    Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
    Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show');
    Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
    Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');

    // Proforma Invoice CRUD
    Route::get('/proformas', [ProformaController::class, 'index'])->name('proformas.index');
    Route::get('/proformas/create', [ProformaController::class, 'create'])->name('proformas.create');
    Route::post('/proformas', [ProformaController::class, 'store'])->name('proformas.store');
    Route::get('/proformas/{proforma}', [ProformaController::class, 'show'])->name('proformas.show');
    Route::get('/proformas/{proforma}/edit', [ProformaController::class, 'edit'])->name('proformas.edit');
    Route::put('/proformas/{proforma}', [ProformaController::class, 'update'])->name('proformas.update');

    // Agent Management
    Route::get('/agents', [AgentController::class, 'index'])->name('agents.index');
    Route::get('/agents/create', [AgentController::class, 'create'])->name('agents.create');
    Route::post('/agents', [AgentController::class, 'store'])->name('agents.store');
    Route::get('/agents/{agent}/edit', [AgentController::class, 'edit'])->name('agents.edit');
    Route::put('/agents/{agent}', [AgentController::class, 'update'])->name('agents.update');
    Route::patch('/api/agents/{agent}/toggle', [AgentController::class, 'toggle'])->name('api.agents.toggle');

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
