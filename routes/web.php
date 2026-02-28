<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', [\App\Http\Controllers\LandingController::class, 'index'])->name('landing');

Route::middleware('auth')->group(function () {
    Route::get('/billing', [\App\Http\Controllers\BillingController::class, 'index'])->name('billing.index');
    Route::post('/billing/checkout', [\App\Http\Controllers\BillingController::class, 'checkout'])->name('billing.checkout');
});

// Asaas Webhook (Skip CSRF)
Route::post('/webhooks/asaas', [\App\Http\Controllers\BillingController::class, 'webhook'])->name('webhooks.asaas')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

Route::get('/dashboard', function () {
    if (auth()->user()->is_admin) {
        return redirect()->route('admin.dashboard');
    }
    $projects = \App\Models\Project::orderBy('created_at', 'desc')->get();
    return Inertia::render('Dashboard', [
        'projects' => $projects
    ]);
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('projects', \App\Http\Controllers\ProjectController::class);
    Route::get('projects/{project}/chat', [\App\Http\Controllers\ProjectController::class, 'chat'])->name('projects.chat');
    Route::get('projects/{project}/status', [\App\Http\Controllers\ProjectController::class, 'status'])->name('projects.status');
    Route::post('projects/{project}/chat', [\App\Http\Controllers\ProjectController::class, 'sendMessage'])->name('projects.send');

    // Tenant User Management
    Route::resource('users', \App\Http\Controllers\TenantUserController::class);

    // Super Admin Routes
    Route::middleware([\App\Http\Middleware\EnsureUserIsAdmin::class])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/tenants', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'tenants'])->name('tenants');
        Route::get('/projects', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'projects'])->name('projects');
        Route::get('/settings', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'settings'])->name('settings');
        Route::post('/settings', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'updateSettings'])->name('settings.update');
        Route::post('/users/{user}/credits', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'adjustCredits'])->name('users.adjust-credits');
    });
});

require __DIR__.'/auth.php';
