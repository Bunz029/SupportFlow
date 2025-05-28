<?php

use App\Http\Controllers\CustomerFeedbackController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KnowledgeBaseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Ticket;
use App\Notifications\TicketAssigned;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Dashboard routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
});

// Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Ticket routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
    Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/tickets/{ticketId}', [TicketController::class, 'show'])->name('tickets.show');
    Route::put('/tickets/{ticket}', [TicketController::class, 'update'])->name('tickets.update');
    Route::post('/tickets/{ticket}/comments', [TicketController::class, 'addComment'])->name('tickets.comments.store');
    Route::get('/tickets/{ticket}/attachments/{attachment}', [TicketController::class, 'downloadAttachment'])->name('tickets.attachments.download');
});

// Feedback routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/tickets/{ticket}/feedback', [CustomerFeedbackController::class, 'create'])->name('feedback.create');
    Route::post('/tickets/{ticket}/feedback', [CustomerFeedbackController::class, 'store'])->name('feedback.store');
    Route::get('/tickets/{ticket}/feedback/{feedback}/edit', [CustomerFeedbackController::class, 'edit'])->name('feedback.edit');
    Route::put('/tickets/{ticket}/feedback/{feedback}', [CustomerFeedbackController::class, 'update'])->name('feedback.update');
    Route::get('/feedback/statistics', [CustomerFeedbackController::class, 'statistics'])->middleware('role:admin,agent')->name('feedback.statistics');
});

// Knowledge Base routes
Route::get('/knowledge-base', [KnowledgeBaseController::class, 'index'])->name('knowledgebase.index');
Route::get('/knowledge-base/{article:slug}', [KnowledgeBaseController::class, 'show'])->name('knowledgebase.show');

// Knowledge Base Admin routes (CRUD)
Route::middleware(['auth', 'verified', 'role:admin,agent'])->group(function () {
    Route::get('/knowledge-base/create', [KnowledgeBaseController::class, 'create'])->name('knowledgebase.create');
    Route::post('/knowledge-base', [KnowledgeBaseController::class, 'store'])->name('knowledgebase.store');
    Route::get('/knowledge-base/{article}/edit', [KnowledgeBaseController::class, 'edit'])->name('knowledgebase.edit');
    Route::put('/knowledge-base/{article}', [KnowledgeBaseController::class, 'update'])->name('knowledgebase.update');
    Route::delete('/knowledge-base/{article}', [KnowledgeBaseController::class, 'destroy'])->name('knowledgebase.destroy');
});

// SLA Management Routes
Route::middleware(['auth', 'role:admin,agent'])->group(function () {
    Route::get('/sla/dashboard', [App\Http\Controllers\SlaController::class, 'dashboard'])->name('sla.dashboard');
    Route::get('/sla/{ticketSla}', [App\Http\Controllers\SlaController::class, 'show'])->name('sla.show');
});

// Report Routes
Route::middleware(['auth', 'role:admin,agent'])->group(function () {
    Route::get('/reports/sla-performance', [App\Http\Controllers\ReportController::class, 'slaPerformance'])->name('reports.sla-performance');
});

// Notification Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/count', [App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('notifications.count');
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::post('/notifications/{id}/mark-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
});

// User Management (Admin)
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/users', [App\Http\Controllers\UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}/edit', [App\Http\Controllers\UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [App\Http\Controllers\UserController::class, 'update'])->name('users.update');
    Route::post('/users/{user}/deactivate', [App\Http\Controllers\UserController::class, 'deactivate'])->name('users.deactivate');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Categories
    Route::resource('categories', App\Http\Controllers\Admin\CategoryController::class);

    // Knowledge Base
    Route::resource('knowledge-base', App\Http\Controllers\Admin\KnowledgeBaseController::class);
    Route::post('knowledge-base/{article}/views', [App\Http\Controllers\Admin\KnowledgeBaseController::class, 'incrementViews'])->name('knowledge-base.views');
    Route::get('knowledge-base/search', [App\Http\Controllers\Admin\KnowledgeBaseController::class, 'search'])->name('knowledge-base.search');

    // Tickets management - moved to main tickets controller with filters
    // Route::resource('tickets', App\Http\Controllers\Admin\TicketController::class);
    
    // Keep ticket rejection functionality
    Route::post('tickets/{ticket}/reject', [App\Http\Controllers\Admin\TicketController::class, 'reject'])->name('tickets.reject');
    Route::delete('tickets/{ticket}', [App\Http\Controllers\Admin\TicketController::class, 'destroy'])->name('tickets.destroy');

    // Settings
    Route::get('settings', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('settings.index');
    Route::put('settings/sla', [App\Http\Controllers\Admin\SettingsController::class, 'updateSla'])->name('settings.update-sla');
    Route::put('settings/notifications', [App\Http\Controllers\Admin\SettingsController::class, 'updateNotifications'])->name('settings.update-notifications');
    Route::get('settings/sla', [App\Http\Controllers\Admin\SettingsController::class, 'getSlaSettings'])->name('settings.get-sla');
    Route::get('settings/notifications', [App\Http\Controllers\Admin\SettingsController::class, 'getNotificationSettings'])->name('settings.get-notifications');
});

Route::get('/test-kernel', function () {
    return 'If you see this, the testkernel middleware did not trigger.';
})->middleware('testkernel');

// Add test route for notifications
Route::get('/test-notification', function () {
    $user = User::first();
    $ticket = Ticket::first();
    
    if (!$user || !$ticket) {
        return "Cannot test - no users or tickets found.";
    }
    
    $notification = new TicketAssigned($ticket);
    
    // Store directly in database
    $user->storeNotification($notification);
    
    return "Notification test complete. Check the database for new notifications.";
});

// Add a diagnostic route to check user role for SLA access
Route::get('/check-sla-access', function () {
    if (!Auth::check()) {
        return "Not logged in. Please login first.";
    }
    
    $user = Auth::user();
    return [
        'user_id' => $user->id,
        'name' => $user->name,
        'email' => $user->email,
        'role' => $user->role,
        'has_agent_role' => $user->role === 'agent',
        'has_admin_role' => $user->role === 'admin',
        'should_see_sla' => in_array($user->role, ['admin', 'agent']),
        'routes_available' => [
            'sla_dashboard' => route('sla.dashboard'),
            'sla_performance' => route('reports.sla-performance'),
        ]
    ];
});

require __DIR__.'/auth.php';
