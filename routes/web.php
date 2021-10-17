<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Master\CompanyController;
use App\Http\Controllers\Master\DivisionController;
use App\Http\Controllers\Master\EmployeeTypeController;
use App\Http\Controllers\Master\LeaveTypeController;
use App\Http\Controllers\Master\PositionController;
use App\Http\Controllers\Role\RoleController;
use Illuminate\Support\Facades\Route;


Route::any('/', function(){
    return redirect()->route('login');
});

Route::group(['middleware' => ['guest']], function(){
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

Route::group(['middleware' => ['auth']], function(){
    Route::get('/logout',[AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::group(['prefix' => 'master', 'as' => 'master.'], function(){

        Route::group(['prefix' => 'company', 'middleware' => 'user_type:admin'], function(){
            Route::get('/',[CompanyController::class, 'index'])->name('company');
            Route::get('/{id}',[CompanyController::class, 'detail'])->name('company.detail');
            Route::delete('/{id}',[CompanyController::class, 'delete'])->name('company.delete');
            Route::post('/data/json',[CompanyController::class, 'dataJson'])->name('company.json');
            Route::post('/',[CompanyController::class, 'updateOrCreate'])->name('company.submit');
        });

        Route::group(['prefix' => 'division'], function(){
            Route::get('/',[DivisionController::class, 'index'])->name('division');
            Route::get('/{id}',[DivisionController::class, 'detail'])->name('division.detail');
            Route::delete('/{id}',[DivisionController::class, 'delete'])->name('division.delete');
            Route::post('/data/json',[DivisionController::class, 'dataJson'])->name('division.json');
            Route::post('/',[DivisionController::class, 'updateOrCreate'])->name('division.submit');
        });

        Route::group(['prefix' => 'position'], function(){
            Route::get('/',[PositionController::class, 'index'])->name('position');
            Route::get('/{id}',[PositionController::class, 'detail'])->name('position.detail');
            Route::delete('/{id}',[PositionController::class, 'delete'])->name('position.delete');
            Route::post('/data/json',[PositionController::class, 'dataJson'])->name('position.json');
            Route::post('/',[PositionController::class, 'updateOrCreate'])->name('position.submit');
        });

        Route::group(['prefix' => 'leave-type'], function(){
            Route::get('/',[LeaveTypeController::class, 'index'])->name('leave-type');
            Route::get('/{id}',[LeaveTypeController::class, 'detail'])->name('leave-type.detail');
            Route::delete('/{id}',[LeaveTypeController::class, 'delete'])->name('leave-type.delete');
            Route::post('/data/json',[LeaveTypeController::class, 'dataJson'])->name('leave-type.json');
            Route::post('/',[LeaveTypeController::class, 'updateOrCreate'])->name('leave-type.submit');
        });

        Route::group(['prefix' => 'employee-type'], function(){
            Route::get('/',[EmployeeTypeController::class, 'index'])->name('employee-type');
            Route::get('/{id}',[EmployeeTypeController::class, 'detail'])->name('employee-type.detail');
            Route::delete('/{id}',[EmployeeTypeController::class, 'delete'])->name('employee-type.delete');
            Route::post('/data/json',[EmployeeTypeController::class, 'dataJson'])->name('employee-type.json');
            Route::post('/',[EmployeeTypeController::class, 'updateOrCreate'])->name('employee-type.submit');
        });
    });

    Route::group(['prefix' => 'roles'], function () {
        Route::get('/', [RoleController::class, 'index'])->middleware('permission:role-read')->name('roles');
        Route::get('/json', [RoleController::class, 'dataJson'])->middleware('permission:role-read')->name('roles.json');
        Route::get('/create', [RoleController::class, 'create'])->middleware('permission:role-create')->name('roles.create');
        Route::post('/create', [RoleController::class, 'store'])->middleware('permission:role-create')->name('roles.simpan');
        Route::get('/edit/{id}', [RoleController::class, 'edit'])->middleware('permission:role-read')->name('roles.edit');
        Route::put('/edit/{id}', [RoleController::class, 'update'])->middleware('permission:role-update')->name('roles.update');
        Route::delete('/delete/{id}', [RoleController::class, 'delete'])->middleware('permission:role-delete')->name('roles.delete');
    });
});


