<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ManagerController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SalesReportController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

Route::redirect('/', '/login');

Route::middleware('auth')->group(function (): void {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/data', [DashboardController::class, 'data'])->name('dashboard.data');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::post('/employee/password/initial', [ProfileController::class, 'updateInitialPassword'])->name('employee.password.initial.update');
    Route::post('/employee/password/initial/later', [ProfileController::class, 'deferInitialPassword'])->name('employee.password.initial.defer');
    Route::get('/employee/dashboard', [DashboardController::class, 'index'])->name('employee.dashboard');
    Route::get('/manager/dashboard', [DashboardController::class, 'index'])->name('manager.dashboard');
    Route::get('/manager/customer-records', [ManagerController::class, 'customerRecords'])->name('manager.customer-records');
    Route::get('/manager/orders-payments', [ManagerController::class, 'ordersAndPayments'])->name('manager.orders-payments');
    Route::get('/manager/services', [ServiceController::class, 'index'])->name('manager.services');
    Route::post('/manager/services', [ServiceController::class, 'store'])->name('manager.services.store');
    Route::delete('/manager/services/{service}', [ServiceController::class, 'destroy'])->name('manager.services.destroy');
    Route::get('/manager/sales-reports', [SalesReportController::class, 'index'])->name('manager.sales-reports');
    Route::get('/manager/sales-reports/export/{format}', [SalesReportController::class, 'export'])->name('manager.sales-reports.export');
    Route::get('/manager/employees', [ManagerController::class, 'employees'])->name('manager.employees');
    Route::post('/manager/employees', [ManagerController::class, 'storeEmployee'])->name('manager.employees.store');
    Route::delete('/manager/employees/{employee}', [ManagerController::class, 'destroyEmployee'])->name('manager.employees.destroy');
    Route::patch('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');

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
