<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketReplyController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TicketImportController;
use App\Http\Controllers\TicketAttachmentController;
use App\Http\Controllers\AdminManagementController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/dashboard', [
        DashboardController::class,
        'index'
    ])->name('dashboard');

    // Admin routes
    Route::middleware(['role:admin'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {

            Route::get('/dashboard', [
                DashboardController::class,
                'admin'
            ])->name('dashboard');

            Route::get('/users', [AdminManagementController::class, 'users'])->name('users.index');
            Route::post('/users', [AdminManagementController::class, 'storeUser'])->name('users.store');
            Route::patch('/users/{user}', [AdminManagementController::class, 'updateUser'])->name('users.update');

            Route::get('/settings', [AdminManagementController::class, 'settings'])->name('settings.index');
            Route::post('/categories', [AdminManagementController::class, 'storeCategory'])->name('categories.store');
            Route::patch('/categories/{category}', [AdminManagementController::class, 'updateCategory'])->name('categories.update');
            Route::delete('/categories/{category}', [AdminManagementController::class, 'destroyCategory'])->name('categories.destroy');
            Route::post('/priorities', [AdminManagementController::class, 'storePriority'])->name('priorities.store');
            Route::patch('/priorities/{priority}', [AdminManagementController::class, 'updatePriority'])->name('priorities.update');
            Route::delete('/priorities/{priority}', [AdminManagementController::class, 'destroyPriority'])->name('priorities.destroy');
            Route::put('/priorities/{priority}/sla-rule', [AdminManagementController::class, 'saveSlaRule'])->name('sla-rules.save');
            Route::put('/business-hours', [AdminManagementController::class, 'saveBusinessHours'])->name('business-hours.save');
            Route::post('/holidays', [AdminManagementController::class, 'storeHoliday'])->name('holidays.store');
            Route::delete('/holidays/{holiday}', [AdminManagementController::class, 'destroyHoliday'])->name('holidays.destroy');
        });

    // Agent routes
    Route::middleware(['role:agent'])
        ->prefix('agent')
        ->name('agent.')
        ->group(function () {

            Route::get('/dashboard', [
                DashboardController::class,
                'agent'
            ])->name('dashboard');
        });

    // Customer routes
    Route::middleware(['role:customer'])
        ->prefix('customer')
        ->name('customer.')
        ->group(function () {

            Route::get('/dashboard', [
                DashboardController::class,
                'customer'
            ])->name('dashboard');
        });


    Route::resource('tickets', TicketController::class)
        ->only([
            'index',
            'create',
            'store',
            'show',
        ]);

    Route::post(
        '/tickets/{ticket}/accept',
        [TicketController::class, 'accept']
    )->name('tickets.accept');

    Route::post(
        '/tickets/{ticket}/assign',
        [TicketController::class, 'assign']
    )->name('tickets.assign');

    Route::post(
        '/tickets/{ticket}/auto-assign',
        [TicketController::class, 'autoAssign']
    )->name('tickets.auto-assign');

    Route::post(
        '/tickets/{ticket}/replies',
        [TicketReplyController::class, 'store']
    )->name('tickets.replies.store');

    Route::post(
        '/tickets/{ticket}/resolve',
        [TicketController::class, 'resolve']
    )->name('tickets.resolve');

    Route::patch(
        '/tickets/{ticket}/status',
        [TicketController::class, 'updateStatus']
    )->name('tickets.status.update');

    Route::post(
        '/tickets/{ticket}/reopen-request',
        [TicketController::class, 'requestReopen']
    )->name('tickets.reopen-request');

    Route::post(
        '/ticket-imports',
        [TicketImportController::class, 'store']
    )->name('ticket-imports.store');

    Route::get(
        '/ticket-imports/{ticketImport}',
        [TicketImportController::class, 'show']
    )->name('ticket-imports.show');

    Route::get(
        '/attachments/{attachment}/download',
        [TicketAttachmentController::class, 'download']
    )->name('attachments.download');

    Route::get(
        '/attachments/{attachment}/preview',
        [TicketAttachmentController::class, 'preview']
    )->name('attachments.preview');
});

require __DIR__ . '/auth.php';
