<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\OrderController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

Route::redirect('/', '/login');

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/employee/dashboard', [DashboardController::class, 'index'])->name('employee.dashboard');
    Route::get('/manager/dashboard', [DashboardController::class, 'index'])->name('manager.dashboard');
    Route::get('/manager/customer-records', [ManagerController::class, 'customerRecords'])->name('manager.customer-records');
    Route::get('/manager/orders-payments', [ManagerController::class, 'ordersAndPayments'])->name('manager.orders-payments');
    Route::get('/manager/sales-reports', [ManagerController::class, 'salesReports'])->name('manager.sales-reports');
    Route::get('/manager/employees', [ManagerController::class, 'employees'])->name('manager.employees');
    Route::post('/manager/employees', [ManagerController::class, 'storeEmployee'])->name('manager.employees.store');

    Route::prefix('employee')->group(function (): void {
        Route::get('/records', [OrderController::class, 'index'])->name('records.index');
        Route::redirect('/orders', '/employee/records');
        Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
        Route::get('/records/{order}', [OrderController::class, 'show'])->name('records.show');
        Route::patch('/records/{order}/status', [OrderController::class, 'updateStatus'])->name('records.status.update');
        Route::patch('/records/{order}/payment', [OrderController::class, 'recordPayment'])->name('records.payment.record');
        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    });
});
