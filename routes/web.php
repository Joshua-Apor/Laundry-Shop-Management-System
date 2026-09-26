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

    Route::prefix('employee')->group(function (): void {
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status.update');
        Route::patch('/orders/{order}/payment', [OrderController::class, 'recordPayment'])->name('orders.payment.record');
        Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    });
});
