<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EquipmentController;
use App\Http\Controllers\BorrowingController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\ReportController;

// Root redirect
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==============================================================================
// 1. ADMIN PORTAL (Ma'am Arbe — Falcons Athletics Administrator)
// ==============================================================================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'admin'])->name('dashboard');

    // Users & Roles
    Route::get('/users', [AdminController::class, 'usersIndex'])->name('users.index');

    // Athletes Directory
    Route::get('/athletes', [AdminController::class, 'athletesIndex'])->name('athletes.index');

    // Coaches Directory
    Route::get('/coaches', [AdminController::class, 'coachesIndex'])->name('coaches.index');

    // Sports Master
    Route::get('/sports', [AdminController::class, 'sportsIndex'])->name('sports.index');
    Route::post('/sports', [AdminController::class, 'sportsStore'])->name('sports.store');

    // Teams & Rosters (Many-to-Many 1)
    Route::get('/teams', [AdminController::class, 'teamsIndex'])->name('teams.index');
    Route::post('/teams', [AdminController::class, 'teamsStore'])->name('teams.store');
    Route::post('/teams/{team}/members', [AdminController::class, 'addTeamMember'])->name('teams.members.add');
    Route::delete('/teams/members/{member}', [AdminController::class, 'removeTeamMember'])->name('teams.members.remove');

    // Equipment Master
    Route::resource('equipment', EquipmentController::class);

    // Borrowing & Returns (Many-to-Many 2, Stored Procedures)
    Route::get('/borrowing', [BorrowingController::class, 'index'])->name('borrowing.index');
    Route::get('/borrowing/create', [BorrowingController::class, 'create'])->name('borrowing.create');
    Route::post('/borrowing', [BorrowingController::class, 'store'])->name('borrowing.store');
    Route::post('/borrowing/return/{item}', [BorrowingController::class, 'processReturn'])->name('borrowing.return');
    Route::post('/borrowing/{transaction}/approve', [BorrowingController::class, 'approve'])->name('borrowing.approve');

    // Maintenance Records
    Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
    Route::get('/maintenance/create', [MaintenanceController::class, 'create'])->name('maintenance.create');
    Route::post('/maintenance', [MaintenanceController::class, 'store'])->name('maintenance.store');
    Route::post('/maintenance/{record}/complete', [MaintenanceController::class, 'complete'])->name('maintenance.complete');

    // Database Triggers Audit Logs
    Route::get('/audit', [AuditLogController::class, 'index'])->name('audit.index');

    // Reports (MySQL Views)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
});

// ==============================================================================
// 2. STAFF PORTAL (Equipment Custodian Desk)
// ==============================================================================
Route::middleware(['auth', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'staff'])->name('dashboard');

    // Equipment View & Edit
    Route::get('/equipment', [EquipmentController::class, 'index'])->name('equipment.index');
    Route::get('/equipment/create', [EquipmentController::class, 'create'])->name('equipment.create');
    Route::post('/equipment', [EquipmentController::class, 'store'])->name('equipment.store');
    Route::get('/equipment/{equipment}/edit', [EquipmentController::class, 'edit'])->name('equipment.edit');
    Route::put('/equipment/{equipment}', [EquipmentController::class, 'update'])->name('equipment.update');

    // Borrowing Dispatch & Returns
    Route::get('/borrowing', [BorrowingController::class, 'index'])->name('borrowing.index');
    Route::get('/borrowing/create', [BorrowingController::class, 'create'])->name('borrowing.create');
    Route::post('/borrowing', [BorrowingController::class, 'store'])->name('borrowing.store');
    Route::post('/borrowing/return/{item}', [BorrowingController::class, 'processReturn'])->name('borrowing.return');
    Route::post('/borrowing/{transaction}/approve', [BorrowingController::class, 'approve'])->name('borrowing.approve');

    // Maintenance
    Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
    Route::get('/maintenance/create', [MaintenanceController::class, 'create'])->name('maintenance.create');
    Route::post('/maintenance', [MaintenanceController::class, 'store'])->name('maintenance.store');
    Route::post('/maintenance/{record}/complete', [MaintenanceController::class, 'complete'])->name('maintenance.complete');
});

// ==============================================================================
// 3. COACH PORTAL (Team Management & Equipment Requests)
// ==============================================================================
Route::middleware(['auth', 'role:coach'])->prefix('coach')->name('coach.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'coach'])->name('dashboard');

    // View Teams & Athletes
    Route::get('/teams', [AdminController::class, 'teamsIndex'])->name('teams.index');
    Route::get('/athletes', [AdminController::class, 'athletesIndex'])->name('athletes.index');

    // Equipment Catalog
    Route::get('/equipment', [EquipmentController::class, 'index'])->name('equipment.index');

    // Request Equipment
    Route::get('/borrowing', [BorrowingController::class, 'index'])->name('borrowing.index');
    Route::get('/borrowing/create', [BorrowingController::class, 'create'])->name('borrowing.create');
    Route::post('/borrowing', [BorrowingController::class, 'store'])->name('borrowing.store');

    // Report Damage
    Route::get('/maintenance', [MaintenanceController::class, 'index'])->name('maintenance.index');
    Route::get('/maintenance/create', [MaintenanceController::class, 'create'])->name('maintenance.create');
    Route::post('/maintenance', [MaintenanceController::class, 'store'])->name('maintenance.store');
});

// ==============================================================================
// 4. STUDENT / ATHLETE PORTAL
// ==============================================================================
Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'student'])->name('dashboard');

    // Browse Available Equipment
    Route::get('/equipment', [EquipmentController::class, 'index'])->name('equipment.index');

    // Request & Track Own Borrowings
    Route::get('/borrowing', [BorrowingController::class, 'index'])->name('borrowing.index');
    Route::get('/borrowing/create', [BorrowingController::class, 'create'])->name('borrowing.create');
    Route::post('/borrowing', [BorrowingController::class, 'store'])->name('borrowing.store');

    // Report Damage
    Route::get('/damage/create', [MaintenanceController::class, 'create'])->name('damage.create');
    Route::post('/damage', [MaintenanceController::class, 'store'])->name('damage.store');
});
