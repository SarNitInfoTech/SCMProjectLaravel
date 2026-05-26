<?php

use App\Http\Controllers\InventoryController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth'])->prefix('inventory')->name('inventory.')->group(function () {
    Route::get('/', [InventoryController::class, 'dashboard'])->name('dashboard');

    // Stocks
    Route::get('/stocks', [InventoryController::class, 'stockList'])->name('stocks.list');
    Route::get('/stocks/create', [InventoryController::class, 'createStock'])->name('stocks.create');
    Route::post('/stocks', [InventoryController::class, 'storeStock'])->name('stocks.store');
    Route::get('/stocks/{id}/edit', [InventoryController::class, 'editStock'])->name('stocks.edit');
    Route::put('/stocks/{id}', [InventoryController::class, 'updateStock'])->name('stocks.update');

    // Movements
    Route::get('/stocks/{id}/movements', [InventoryController::class, 'movements'])->name('stocks.movements');
    Route::get('/stocks/{id}/movements/create', [InventoryController::class, 'createMovement'])->name('stocks.movements.create');
    Route::post('/stocks/{id}/movements', [InventoryController::class, 'storeMovement'])->name('stocks.movements.store');

    // Reports
    Route::get('/reports/low-stock', [InventoryController::class, 'lowStockReport'])->name('reports.low-stock');
    Route::get('/reports/movements', [InventoryController::class, 'movementReport'])->name('reports.movements');
});
