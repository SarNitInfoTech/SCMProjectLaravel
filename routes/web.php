<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DepartmentHeadController;
use App\Http\Controllers\IndentController;
use App\Http\Controllers\PORegisterController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\UnitController;
use Illuminate\Support\Facades\Route;

// =======================
// Public Routes
// =======================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// =======================
// Protected Routes
// =======================
Route::middleware(['auth'])->group(function () {

    // ===== Dashboard =====
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::resource('dashboard', DashboardController::class)->only(['index']);

    // ===== Profile =====
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    // ===== Departments =====
    Route::resource('departments', DepartmentController::class);
    Route::get('/department', [DepartmentController::class, 'index'])->name('department.index'); // Optional duplicate

    // ===== Units =====
    Route::resource('units', UnitController::class);
    Route::get('/unit', [UnitController::class, 'index'])->name('unit.index'); // Optional duplicate

    // ===== Projects =====
    Route::resource('projects', ProjectController::class);
    Route::get('/project', [ProjectController::class, 'index'])->name('project.index'); // Optional duplicate

    // ===== Indents =====
    Route::post('/check-indent-exists', [IndentController::class, 'checkIndentExists'])->name('indent.check');
    Route::get('/indent-register/{id}/edit', [IndentController::class, 'editForm'])->name('indent-register.edit');
    Route::put('/indent-register/{id}', [IndentController::class, 'indentRegisterUpdate'])->name('indent-register.indentRegisterUpdate');

    Route::get('/indent-registers', [IndentController::class, 'index'])->name('indent.index');
    Route::get('/indents/create', [IndentController::class, 'create'])->name('indent.create');
    Route::post('/indents/store', [IndentController::class, 'store'])->name('indent.store');

    Route::post('/indents/generate-token', [IndentController::class, 'generateToken'])->name('indent.token'); // (old)
    Route::post('/indent/token', [IndentController::class, 'generateToken'])->name('indent.token'); // (duplicate)
    Route::post('/indentregister', [IndentController::class, 'registerStore'])->name('indent-register.store');

    Route::get('/indent/form', [IndentController::class, 'createForm'])->name('indent.create.form');
    Route::post('/indent/fill', [IndentController::class, 'redirectToForm'])->name('indent.redirect.to.form');
    Route::post('/indent/redirect', [IndentController::class, 'redirectToForm'])->name('indent.redirect.to.form'); // (duplicate)

    // ===== PO Register =====
    Route::resource('po-register', PORegisterController::class);
    Route::get('/indents/po/index', [PORegisterController::class, 'index'])->name('indentroview.index');
    Route::get('po-register/indent/{indent_id}/department/{department_id}', [PORegisterController::class, 'viewByIndent'])->name('po-register.viewByIndent');
    Route::get('/po/export/excel/{indent_id}/{department_id}', [PORegisterController::class, 'downloadPORegisterExcel'])->name('po.export.excel');
    Route::get('/po/export/pdf/{indent_id}/{department_id}', [PORegisterController::class, 'downloadPORegisterPDF'])->name('po.export.pdf');

    // ===== Department Heads =====
    Route::resource('department-head', DepartmentHeadController::class);
    Route::get('department-head/list', [DepartmentHeadController::class, 'index'])->name('departmentHead.list');
    Route::get('department-head/create', [DepartmentHeadController::class, 'create'])->name('departmentHead.create');
    

    Route::prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index'); // All Notifications page
    Route::post('/mark-all-read', [NotificationController::class, 'markAllRead'])->name('markAllRead'); // Optional
});
Route::get('/notifications/mark-all-read', [NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');

    // ❌ Incorrect: GET used for store — remove this route
    // Route::get('departments-heads/store', [DepartmentHeadController::class, 'store'])->name('departmentHead.store');
    // ✅ Already handled by POST from resource:
    // POST /department-heads → department-heads.store

    // ===== Fallback =====
    Route::fallback(function () {
        return redirect()->route('dashboard.index');
    });
});
