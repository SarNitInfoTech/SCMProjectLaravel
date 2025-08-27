<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    DashboardController,
    DepartmentController,
    DepartmentHeadController,
    IndentController,
    PORegisterController,
    ProfileController,
    ProjectController,
    ReportController,
    UserController,
    VendorController,
    ItemController,
    NotificationController,
    UnitController
};

// =======================
// 🔓 Public Routes
// =======================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// =======================
// 🔐 Protected Routes
// =======================
Route::middleware(['auth'])->group(function () {

    // -----------------------
    // 📊 Dashboard
    // -----------------------
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::resource('dashboard', DashboardController::class)->only(['index']);
    Route::get('/stats/filter', [DashboardController::class, 'filter'])
    ->name('stats.filter');

    // -----------------------
    // 🙍‍♂️ Profile
    // -----------------------
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'show'])->name('show');
        Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
        Route::post('/update', [ProfileController::class, 'update'])->name('update');
    });

    // -----------------------
    // 🏢 Departments
    // -----------------------
    Route::resource('departments', DepartmentController::class);
    Route::get('/department', [DepartmentController::class, 'index'])->name('department.index'); // optional alias

    // -----------------------
    // 📐 Units
    // -----------------------
    Route::resource('units', UnitController::class);
    Route::get('/unit', [UnitController::class, 'index'])->name('unit.index'); // optional alias

    // -----------------------
    // 📁 Projects
    // -----------------------
    Route::resource('projects', ProjectController::class);
    Route::get('/project', [ProjectController::class, 'index'])->name('project.index'); // optional alias

    // -----------------------
    // 📝 Indents
    // -----------------------
    Route::prefix('indents')->name('indent.')->group(function () {
        Route::get('/create', [IndentController::class, 'create'])->name('create');
        Route::post('/store', [IndentController::class, 'store'])->name('store');
        Route::post('/generate-token', [IndentController::class, 'generateToken'])->name('token');
    });

    Route::get('/indent/form', [IndentController::class, 'createForm'])->name('indent.create.form');
    Route::get('/indent', [IndentController::class, 'index'])->name('indent.index');
    Route::post('/indent/fill', [IndentController::class, 'redirectToForm'])->name('indent.redirect.to.form');

    // Indent Register Routes
    Route::prefix('indent-register')->name('indent-register.')->group(function () {
        Route::get('/', [IndentController::class, 'index'])->name('index');
        Route::post('/', [IndentController::class, 'registerStore'])->name('store');
        Route::get('/{id}/edit', [IndentController::class, 'editForm'])->name('edit');
        Route::put('/{id}', [IndentController::class, 'indentRegisterUpdate'])->name('indentRegisterUpdate');
    });

    Route::post('/check-indent-exists', [IndentController::class, 'checkIndentExists'])->name('indent.check');

    // -----------------------
    // 🧾 PO Register
    // -----------------------
    Route::resource('po-register', PORegisterController::class);
    Route::get('/indents/po/index', [PORegisterController::class, 'index'])->name('indentroview.index');
    Route::get('/indents/po/{id}/addinvoice', [PORegisterController::class, 'createInvoiceById'])
    ->name('indentroview.createInvoiceById');
    Route::get('po-register/indent/{indent_id}/department/{department_id}', [PORegisterController::class, 'viewByIndent'])->name('po-register.viewByIndent');
    Route::get('/po/export/excel/{indent_id}/{department_id}', [PORegisterController::class, 'downloadPORegisterExcel'])->name('po.export.excel');
    Route::get('/po/export/pdf/{indent_id}/{department_id}', [PORegisterController::class, 'downloadPORegisterPDF'])->name('po.export.pdf');
    Route::post('/po-register/update-status', [PORegisterController::class, 'updateStatus'])->name('po-register.updateStatus');
    Route::post('/po-register/status-pending', [PORegisterController::class, 'statusPending'])->name('po-register.statusPending');
    Route::post('/po-register/status-close', [PORegisterController::class, 'statusClose'])->name('po-register.statusClose');
    Route::post('/po-register/status-cancel', [PORegisterController::class, 'statusCancel'])->name('po-register.statusCancel');
    Route::put('/po-register/{id}', [PORegisterController::class, 'updateInvoice'])->name('po-register.updateInvoice');
    Route::patch('/po-register/{id}', [PORegisterController::class, 'updateById'])
    ->name('po-register.updatePObyId');

    // -----------------------
    // 🧑‍💼 Department Heads
    // -----------------------
    Route::resource('department-head', DepartmentHeadController::class);
    Route::get('department-head/list', [DepartmentHeadController::class, 'index'])->name('departmentHead.list');
    Route::get('department-head/create', [DepartmentHeadController::class, 'create'])->name('departmentHead.create');

    // -----------------------
    // 👥 Users
    // -----------------------
    Route::get('/users/list', [UserController::class, 'index'])->name('users.list');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [UserController::class, 'update'])->name('users.update');

    // -----------------------
    // 🏪 Vendors
    // -----------------------
    Route::get('/vendors/list', [VendorController::class, 'index'])->name('vendors.list');
    Route::get('/vendors/create', [VendorController::class, 'create'])->name('vendors.create');
    Route::post('/vendors', [VendorController::class, 'store'])->name('vendors.store');
    Route::get('/vendors/{vendor}/edit', [VendorController::class, 'edit'])->name('vendors.edit');
    Route::put('/vendors/{vendor}', [VendorController::class, 'update'])->name('vendors.update');
    Route::delete('/vendors/{vendor}', [VendorController::class, 'destroy'])->name('vendors.destroy');
    Route::get('/vendors/{vendor}', [VendorController::class, 'show'])->name('vendors.show');
    // -----------------------
    // 🏪 Vendors
    // -----------------------
    Route::get('/items/list', [ItemController::class, 'index'])->name('items.list');
    Route::get('/items/list', [ItemController::class, 'index'])->name('items.index');
    Route::get('/items/create', [ItemController::class, 'create'])->name('items.create');
    Route::post('/items', [ItemController::class, 'store'])->name('items.store');
    Route::get('/items/{item}/edit', [ItemController::class, 'edit'])->name('items.edit');
    Route::put('/items/{item}', [ItemController::class, 'update'])->name('items.update');
    Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');
    Route::get('/items/{item}', [ItemController::class, 'show'])->name('items.show');
    // -----------------------
    // 🔔 Notifications
    // -----------------------
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/mark-all-read', [NotificationController::class, 'markAllRead'])->name('markAllRead');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllRead']);
    });

    // -----------------------
    // 📊 Reports
    // -----------------------
    Route::prefix('report')->name('report.')->group(function () {
        Route::get('/view', [ReportController::class, 'viewReport'])->name('view');
        Route::get('/view-all-indent', [ReportController::class, 'viewAllIndent'])->name('viewAllIndent');
        Route::get('/export-excel', [ReportController::class, 'exportExcel'])->name('export.excel');
    });

    // routes/web.php
    Route::get('/reports/indents/filter', [ReportController::class, 'filterAllIndentAjax'])
    ->name('reports.indents.filter');
    Route::get('/reports/all/indents/filter', [ReportController::class, 'allIndentAndPOlist'])
    ->name('reports.all.indents.filter');
    Route::get('/reports/indents-po',          [ReportController::class, 'allIndentAndPOlist'])->name('reports.indentspo.index');
Route::get('/reports/indents-pos',        [ReportController::class, 'allIndentAndPOlist'])
    ->name('reports.indentspos.index');

Route::get('/reports/indents-pos/filter', [ReportController::class, 'filterAllIndentPOAjax'])
    ->name('reports.indentspos.filter');

    // -----------------------
    // 🚫 Fallback
    // -----------------------
    Route::fallback(function () {
        return redirect()->route('dashboard.index');
    });
});
