<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Master\CompanyController;
use App\Http\Controllers\Master\DivisionController;
use App\Http\Controllers\Master\PositionController;
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
        Route::group(['prefix' => 'company'], function(){
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
    });
});


