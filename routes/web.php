<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ProfileController;

use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\UserController as AdminUser;
use App\Http\Controllers\Admin\EquipmentController as AdminEquipment;
use App\Http\Controllers\Admin\AuditLogController as AdminAudit;
use App\Http\Controllers\Admin\ReportController as AdminReport;

use App\Http\Controllers\Staff\DashboardController as StaffDashboard;
use App\Http\Controllers\Staff\CheckoutController as StaffCheckout;
use App\Http\Controllers\Staff\ReturnController as StaffReturn;
use App\Http\Controllers\Staff\ReservationReviewController as StaffReservation;
use App\Http\Controllers\Staff\MaintenanceController as StaffMaintenance;

use App\Http\Controllers\Coach\DashboardController as CoachDashboard;
use App\Http\Controllers\Coach\TeamController as CoachTeam;
use App\Http\Controllers\Coach\ReservationController as CoachReservation;
use App\Http\Controllers\Coach\AssignmentController as CoachAssignment;

use App\Http\Controllers\Athlete\DashboardController as AthleteDashboard;
use App\Http\Controllers\Athlete\CatalogController as AthleteCatalog;
use App\Http\Controllers\Athlete\MyLoanController as AthleteLoan;
use App\Http\Controllers\Athlete\DamageReportController as AthleteDamage;

// Public Root & Authentication
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Authenticated Common Routes (Profile)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
});

// ROLE 1: SYSTEM ADMINISTRATOR (/admin/*)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');

    // Users
    Route::get('/users', [AdminUser::class, 'index'])->name('users.index');
    Route::get('/users/create', [AdminUser::class, 'create'])->name('users.create');
    Route::post('/users', [AdminUser::class, 'store'])->name('users.store');
    Route::patch('/users/{user}/toggle', [AdminUser::class, 'toggleStatus'])->name('users.toggle');

    // Equipment
    Route::resource('equipment', AdminEquipment::class);

    // Audit Logs
    Route::get('/audit-logs', [AdminAudit::class, 'index'])->name('audit.index');

    // Reports
    Route::get('/reports', [AdminReport::class, 'index'])->name('reports.index');
});

// ROLE 2: EQUIPMENT STAFF (/staff/*)
Route::middleware(['auth', 'role:staff,admin'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/dashboard', [StaffDashboard::class, 'index'])->name('dashboard');

    // Checkouts
    Route::get('/checkout', [StaffCheckout::class, 'index'])->name('checkout.index');
    Route::get('/checkout/create', [StaffCheckout::class, 'create'])->name('checkout.create');
    Route::post('/checkout', [StaffCheckout::class, 'store'])->name('checkout.store');

    // Returns & Inspection
    Route::get('/returns', [StaffReturn::class, 'index'])->name('returns.index');
    Route::post('/returns/{item}', [StaffReturn::class, 'process'])->name('returns.process');

    // Reservations Review
    Route::get('/reservations', [StaffReservation::class, 'index'])->name('reservations.index');
    Route::get('/reservations/{reservation}', [StaffReservation::class, 'show'])->name('reservations.show');
    Route::post('/reservations/{reservation}/review', [StaffReservation::class, 'review'])->name('reservations.review');

    // Maintenance Work Orders
    Route::get('/maintenance', [StaffMaintenance::class, 'index'])->name('maintenance.index');
    Route::post('/maintenance', [StaffMaintenance::class, 'store'])->name('maintenance.store');
    Route::patch('/maintenance/{record}/status', [StaffMaintenance::class, 'updateStatus'])->name('maintenance.status');
});

// ROLE 3: COACH (/coach/*)
Route::middleware(['auth', 'role:coach,admin'])->prefix('coach')->name('coach.')->group(function () {
    Route::get('/dashboard', [CoachDashboard::class, 'index'])->name('dashboard');

    // Teams & Roster
    Route::get('/teams', [CoachTeam::class, 'index'])->name('teams.index');
    Route::get('/teams/{team}', [CoachTeam::class, 'show'])->name('teams.show');

    // Team Reservations
    Route::get('/reservations', [CoachReservation::class, 'index'])->name('reservations.index');
    Route::get('/reservations/create', [CoachReservation::class, 'create'])->name('reservations.create');
    Route::post('/reservations', [CoachReservation::class, 'store'])->name('reservations.store');

    // Equipment Assignment
    Route::get('/assignments', [CoachAssignment::class, 'index'])->name('assignments.index');
    Route::get('/assignments/create', [CoachAssignment::class, 'create'])->name('assignments.create');
    Route::post('/assignments', [CoachAssignment::class, 'store'])->name('assignments.store');
});

// ROLE 4: ATHLETE (/athlete/*)
Route::middleware(['auth', 'role:athlete,admin'])->prefix('athlete')->name('athlete.')->group(function () {
    Route::get('/dashboard', [AthleteDashboard::class, 'index'])->name('dashboard');

    // Equipment Catalog & Request
    Route::get('/catalog', [AthleteCatalog::class, 'index'])->name('catalog.index');
    Route::post('/catalog/request', [AthleteCatalog::class, 'requestEquipment'])->name('catalog.request');

    // Personal Loans & Requests
    Route::get('/my-loans', [AthleteLoan::class, 'loans'])->name('loans.index');
    Route::get('/my-requests', [AthleteLoan::class, 'requests'])->name('requests.index');

    // Damage Reports
    Route::get('/damage-reports', [AthleteDamage::class, 'index'])->name('damage.index');
    Route::get('/damage-reports/create', [AthleteDamage::class, 'create'])->name('damage.create');
    Route::post('/damage-reports', [AthleteDamage::class, 'store'])->name('damage.store');
});
