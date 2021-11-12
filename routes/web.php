<?php

use App\Http\Controllers\APIKey\KeyController;
use App\Http\Controllers\Attendance\AttendanceController;
use App\Http\Controllers\Attendance\ConfigController;
use App\Http\Controllers\Attendance\HistoryController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\Employee\EmployeeController;
use App\Http\Controllers\Leave\ApprovalController;
use App\Http\Controllers\Leave\ApprovalRequestController;
use App\Http\Controllers\Leave\LeaveController;
use App\Http\Controllers\Leave\QuotaController;
use App\Http\Controllers\Leave\RequestController;
use App\Http\Controllers\Master\CompanyController;
use App\Http\Controllers\Master\DivisionController;
use App\Http\Controllers\Master\EmployeeLevelController;
use App\Http\Controllers\Master\EmployeeTypeController;
use App\Http\Controllers\Master\LeaveTypeController;
use App\Http\Controllers\Master\PositionController;
use App\Http\Controllers\Role\RoleController;
use App\Http\Controllers\User\UserController;
use Carbon\CarbonPeriod;
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
    Route::post('/change-password',[AuthController::class, 'changePassword'])->name('change-password');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::group(['prefix' => 'master', 'as' => 'master.'], function(){

        Route::group(['prefix' => 'company', 'middleware' => 'user_type:admin'], function(){
            Route::get('/',[CompanyController::class, 'index'])->middleware('permission:mst-perusahaan-read')->name('company');
            Route::get('/{id}',[CompanyController::class, 'detail'])->middleware('permission:mst-perusahaan-read')->name('company.detail');
            Route::post('/{id}',[CompanyController::class, 'updateStatus'])->middleware('permission:mst-perusahaan-update')->name('company.status');
            Route::delete('/{id}',[CompanyController::class, 'delete'])->middleware('permission:mst-perusahaan-delete')->name('company.delete');
            Route::post('/data/json',[CompanyController::class, 'dataJson'])->middleware('permission:mst-perusahaan-read')->name('company.json');
            Route::post('/',[CompanyController::class, 'updateOrCreate'])->middleware('permission:mst-perusahaan-update|mst-perusahaan-create')->name('company.submit');
        });

        Route::group(['prefix' => 'division'], function(){
            Route::get('/',[DivisionController::class, 'index'])->middleware('permission:mst-divisi-read')->name('division');
            Route::get('/{id}',[DivisionController::class, 'detail'])->middleware('permission:mst-divisi-read')->name('division.detail');
            Route::delete('/{id}',[DivisionController::class, 'delete'])->middleware('permission:mst-divisi-delete')->name('division.delete');
            Route::post('/data/json',[DivisionController::class, 'dataJson'])->middleware('permission:mst-divisi-read')->name('division.json');
            Route::post('/',[DivisionController::class, 'updateOrCreate'])->middleware('permission:mst-divisi-create|mst-divisi-update')->name('division.submit');
        });

        Route::group(['prefix' => 'position'], function(){
            Route::get('/',[PositionController::class, 'index'])->middleware('permission:mst-jabatan-read')->name('position');
            Route::get('/{id}',[PositionController::class, 'detail'])->middleware('permission:mst-jabatan-read')->name('position.detail');
            Route::delete('/{id}',[PositionController::class, 'delete'])->middleware('permission:mst-jabatan-delete')->name('position.delete');
            Route::post('/data/json',[PositionController::class, 'dataJson'])->middleware('permission:mst-jabatan-read')->name('position.json');
            Route::post('/',[PositionController::class, 'updateOrCreate'])->middleware('permission:mst-jabatan-update|mst-jabatan-create')->name('position.submit');
        });

        Route::group(['prefix' => 'leave-type'], function(){
            Route::get('/',[LeaveTypeController::class, 'index'])->middleware('permission:mst-jenis-cuti-read')->name('leave-type');
            Route::get('/{id}',[LeaveTypeController::class, 'detail'])->middleware('permission:mst-jenis-cuti-read')->name('leave-type.detail');
            Route::delete('/{id}',[LeaveTypeController::class, 'delete'])->middleware('permission:mst-jenis-cuti-delete')->name('leave-type.delete');
            Route::post('/data/json',[LeaveTypeController::class, 'dataJson'])->middleware('permission:mst-jenis-cuti-read')->name('leave-type.json');
            Route::post('/',[LeaveTypeController::class, 'updateOrCreate'])->middleware('permission:mst-jenis-cuti-create|mst-jenis-cuti-update')->name('leave-type.submit');
        });

        Route::group(['prefix' => 'employee-type'], function(){
            Route::get('/',[EmployeeTypeController::class, 'index'])->middleware('permission:mst-status-karyawan-read')->name('employee-type');
            Route::get('/{id}',[EmployeeTypeController::class, 'detail'])->middleware('permission:mst-status-karyawan-read')->name('employee-type.detail');
            Route::delete('/{id}',[EmployeeTypeController::class, 'delete'])->middleware('permission:mst-status-karyawan-delete')->name('employee-type.delete');
            Route::post('/data/json',[EmployeeTypeController::class, 'dataJson'])->middleware('permission:mst-status-karyawan-read')->name('employee-type.json');
            Route::post('/',[EmployeeTypeController::class, 'updateOrCreate'])->middleware('permission:mst-status-karyawan-create|mst-status-karyawan-update')->name('employee-type.submit');
        });

        Route::group(['prefix' => 'employee-level'], function(){
            Route::get('/',[EmployeeLevelController::class, 'index'])->middleware('permission:mst-golongan-karyawan-read')->name('employee-level');
            Route::get('/{id}',[EmployeeLevelController::class, 'detail'])->middleware('permission:mst-golongan-karyawan-read')->name('employee-level.detail');
            Route::delete('/{id}',[EmployeeLevelController::class, 'delete'])->middleware('permission:mst-golongan-karyawan-delete')->name('employee-level.delete');
            Route::post('/data/json',[EmployeeLevelController::class, 'dataJson'])->middleware('permission:mst-golongan-karyawan-read')->name('employee-level.json');
            Route::post('/',[EmployeeLevelController::class, 'updateOrCreate'])->middleware('permission:mst-golongan-karyawan-create|mst-golongan-karyawan-update')->name('employee-level.submit');
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

    Route::group(['prefix' => 'employee'], function() {
        Route::get('/',[EmployeeController::class, 'index'])->middleware('permission:data-karyawan-read')->name('employee');
        Route::get('/{id}',[EmployeeController::class, 'detail'])->middleware('permission:data-karyawan-read')->name('employee.detail');
        Route::delete('/{id}',[EmployeeController::class, 'delete'])->middleware('permission:data-karyawan-delete')->name('employee.delete');
        Route::post('/',[EmployeeController::class, 'updateOrCreate'])->middleware('permission:data-karyawan-create|data-karyawan-update')->name('employee.submit');
        Route::post('/{id}',[EmployeeController::class, 'updateStatus'])->middleware('permission:data-karyawan-update')->name('employee.status');
        Route::post('/data/json',[EmployeeController::class, 'dataJson'])->middleware('permission:data-karyawan-read')->name('employee.json');
    });

    Route::group(['prefix' => 'data', 'as' => 'data.'], function(){
        Route::get('division/{company_id}',[DataController::class, 'division'])->name('division');
        Route::get('position/{company_id}',[DataController::class, 'position'])->name('position');
        Route::get('employee-level/{company_id}',[DataController::class, 'employeeLevel'])->name('employee-level');
        Route::get('employee-type/{company_id}',[DataController::class, 'employeeType'])->name('employee-type');
        Route::get('leave-type/{company_id}',[DataController::class, 'leaveType'])->name('leave-type');
        Route::get('employees-no-role',[DataController::class, 'employeesNoRole'])->name('employee');
        Route::get('time-config/{company_id}',[ConfigController::class, 'getTimeConfig'])->name('time-config');
        Route::get('employees/{company_id}',[DataController::class, 'getEmployee'])->name('employees');
    });

    Route::group(['prefix' => 'user'], function(){
        Route::get('/',[UserController::class, 'index'])->middleware('permission:user-read')->name('user');
        Route::get('/{id}',[UserController::class, 'detail'])->middleware('permission:user-read')->name('user.detail');
        Route::delete('/{id}',[UserController::class, 'delete'])->middleware('permission:user-delete')->name('user.delete');
        Route::post('/',[UserController::class, 'updateOrCreate'])->middleware('permission:user-create|user-update')->name('user.submit');
        Route::post('/data/json',[UserController::class, 'dataJson'])->middleware('permission:user-read')->name('user.json');
    });

    Route::group(['prefix' => 'user-employee'], function(){
        Route::post('assign-role',[UserController::class, 'assignRoleToEmployee'])->middleware('permission:user-create|user-update')->name('user-employee.submit');
        Route::delete('delete/{model_id}/{role_id}',[UserController::class, 'deleteEmployeeRole'])->middleware('permission:user-delete')->name('user-employee.delete');
    });

    Route::group(['prefix' => 'attendance-leave', 'as' => 'al.'], function(){
        Route::get('time-config',[ConfigController::class, 'index'])->middleware('permission:setting-absensi-read')->name('time-config');
        Route::post('time-config',[ConfigController::class, 'updateTimeConfig'])->middleware('permission:setting-absensi-create|setting-absensi-update')->name('time-config.submit');

        Route::get('leave-quota',[QuotaController::class, 'index'])->middleware('permission:jatah-cuti-read')->name('leave-quota');
        Route::get('leave-quota/{employee_id}',[QuotaController::class, 'getQuota'])->middleware('permission:jatah-cuti-read')->name('leave-quota-employee');
        Route::post('leave-quota/{employee_id}',[QuotaController::class, 'setQuota'])->middleware('permission:jatah-cuti-create|jatah-cuti-update')->name('leave-quota-employee.submit');
        Route::post('/data/json',[EmployeeController::class, 'dataJson'])->middleware('permission:jatah-cuti-read')->name('employee.json');

        Route::group(['middleware' => ['user_type:employee']], function(){
            Route::get('request-leave',[RequestController::class, 'index'])->name('request-leave');
            Route::get('request-leave/{id}',[RequestController::class, 'detail'])->name('request-leave.detail');
            Route::delete('request-leave/{id}',[RequestController::class, 'delete'])->name('request-leave.delete');
            Route::post('request-leave',[RequestController::class, 'updateOrCreate'])->name('request-leave.submit');
            Route::post('request-leave/data/json',[RequestController::class, 'dataJson'])->name('request-leave.json');

            Route::get('attendance',[AttendanceController::class, 'index'])->name('attendance');
            Route::post('attendance',[AttendanceController::class, 'setAttendance'])->name('attendance.submit');

            Route::get('leave-request',[LeaveController::class,'index'])->name('leave-request');
            Route::get('leave-request/{id}',[LeaveController::class,'findById'])->name('leave-request.detail');
            Route::delete('leave-request/{id}',[LeaveController::class,'delete'])->name('leave-request.delete');
            Route::post('leave-request',[LeaveController::class,'updateOrCreate'])->name('leave-request.submit');
            Route::post('leave-request/data/json',[LeaveController::class,'dataJson'])->name('leave-request.json');
        });
        
        Route::get('employee-leave',[ApprovalController::class, 'index'])->middleware('permission:cuti-read')->name('approval');
        Route::post('employee-leave',[ApprovalController::class, 'updateStatus'])->middleware('permission:cuti-create|cuti-update')->name('approval.submit');
        Route::post('employee-leave/data/json',[ApprovalController::class, 'dataJson'])->middleware('permission:cuti-read')->name('approval.json');

        Route::get('employee-leave-request',[ApprovalRequestController::class, 'index'])->middleware('permission:izin-read')->name('approval-request');
        Route::post('employee-leave-request',[ApprovalRequestController::class, 'updateStatus'])->middleware('permission:izin-create|izin-update')->name('approval-request.submit');
        Route::post('employee-leave-request/data/json',[ApprovalRequestController::class, 'dataJson'])->middleware('permission:izin-read')->name('approval-request.json');

        Route::get('history-attendance', [HistoryController::class, 'index'])->middleware('permission:absensi-karyawan-read')->name('history');
        Route::post('history-attendance/data/json', [HistoryController::class, 'dataJson'])->middleware('permission:absensi-karyawan-read')->name('history.json');
        Route::get('history-attendance/export',[HistoryController::class, 'toExcel'])->middleware('permission:absensi-karyawan-read')->name('history.excel');
    });

    Route::group(['prefix' => 'api-key'], function(){
        Route::get('/',[KeyController::class, 'index'])->middleware('permission:api-key-read')->name('apikey');
        Route::post('/',[KeyController::class, 'create'])->middleware('permission:api-key-create')->name('apikey.create');
        Route::put('/',[KeyController::class, 'update'])->middleware('permission:api-key-update')->name('apikey.update');
        Route::post('/{id}',[KeyController::class, 'changeStatus'])->middleware('permission:api-key-update|api-key-create')->name('apikey.change-status');
        Route::delete('/{id}',[KeyController::class, 'delete'])->middleware('permission:api-key-delete')->name('apikey.delete');
        Route::post('/data/json',[KeyController::class, 'dataJson'])->middleware('permission:api-key-read')->name('apikey.json');
    });
});
