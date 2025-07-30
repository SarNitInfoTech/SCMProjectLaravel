<?php
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\IndentController;

use Illuminate\Support\Facades\Route;



Route::get('/login', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(callback: function () {
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::resource('departments', DepartmentController::class);
    Route::resource('units', UnitController::class);
    Route::resource('projects', ProjectController::class);
    Route::resource('dashboard', DashboardController::class);
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/unit', [UnitController::class, 'index'])->name('unit.index');
    Route::get('/project', [ProjectController::class, 'index'])->name('project.index');
    Route::get('/department', [DepartmentController::class, 'index'])->name('department.index');
    Route::get('/indents/create', [IndentController::class, 'create'])->name('indent.create');
    Route::post('/indents/store', [IndentController::class, 'store'])->name('indent.store');
    Route::post('/indents/generate-token', [IndentController::class, 'generateToken'])->name('indent.token');

    Route::post('/indent/token', [IndentController::class, 'generateToken'])->name('indent.token');
    Route::post('/indentregister', [IndentController::class, 'registerStore'])->name('indent-register.store');
    Route::get('/indent/form', [IndentController::class, 'createForm'])->name('indent.create.form');
    Route::post('/indent/fill', [IndentController::class, 'redirectToForm'])->name('indent.redirect.to.form');
    Route::post('/indent/redirect', [IndentController::class, 'redirectToForm'])->name('indent.redirect.to.form');






    Route::fallback(function () {
    return redirect()->route('dashboard.index');
});
});
