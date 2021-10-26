<?php 
namespace App\Repositories;

use App\Models\Employee;
use App\Models\User;
use App\Support\CodeGenerator;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;

class EmployeeRepo
{
    public function getAll()
    {
        return Employee::auth()->with(
            'division:id,name',
            'company:id,name',
            'position:id,name',
            'type:id,name',
            'level:id,name',
        )->get();
    }

    public function getEmployeeNoRole()
    {
        return User::select('id')->auth()->doesnthave('role')->has('employee')->with('employee:id,user_id,employee_code,name')->where('user_type','employee')->get();
    }

    public function findById($id)
    {
        return Employee::auth()->with('user:id,username')->findOrFail($id);
    }

    public function create($request)
    {
        $avatar = null;
        if($request->hasFile('fotoKaryawan')) {
            $avatar = Str::random(20).'.'.$request->fotoKaryawan->getClientOriginalExtension();
            $request->file('fotoKaryawan')->storeAs('foto-karyawan', $avatar, 'public');
        }

        $ktp = null;
        if($request->hasFile('fotoKtp')) {
            $ktp = Str::random(20).'.'.$request->fotoKtp->getClientOriginalExtension();
            $request->file('fotoKtp')->storeAs('foto-ktp', $ktp, 'public');
        }

        $skck = null;
        if($request->hasFile('fotoSkck')) {
            $skck = Str::random(20).'.'.$request->fotoSkck->getClientOriginalExtension();
            $request->file('fotoSkck')->storeAs('foto-skck', $skck, 'public');
        }

        $kontrak = null;
        if($request->hasFile('kontrakKerja')) {
            $kontrak = Str::random(20).'.'.$request->kontrakKerja->getClientOriginalExtension();
            $request->file('kontrakKerja')->storeAs('kontrak', $kontrak, 'public');
        }

        $company_id = (auth()->user()->user_type == 'admin') ? $request->perusahaan : auth()->user()->company_id;

        DB::beginTransaction();
        try {
            $user = User::create([
                'company_id' => $company_id,
                'username'   => $request->username,
                'password'   => Hash::make($request->password),
                'user_type'  => 'employee'
            ]);
        } catch (QueryException $e) {
            DB::rollBack();
            Log::warning($e->getMessage());
            throw $e;
        }

        $employee_permissions = [
            'absen-cuti-read'
            // 'cuti-create',
            // 'cuti-read',
            // 'cuti-update',
            // 'cuti-delete',
            // 'absensi-create',
            // 'absensi-read',
            // 'absensi-update',
            // 'absensi-delete',
        ];

        try {
            
            $user->syncPermissions($employee_permissions);

        } catch (QueryException $e) {

            DB::rollBack();
            Log::warning($e->getMessage());
            throw $e;

        } catch (PermissionDoesNotExist $e) {

            DB::rollBack();
            Log::warning($e->getMessage());
            throw $e;
        }

        try {
            $employee = Employee::create([
                'user_id'               => $user->id,
                'company_id'            => $company_id,
                'division_id'           => $request->divisi,
                'position_id'           => $request->jabatan,
                'employee_type_id'      => $request->status,
                'level_id'              => $request->golongan,
                'employee_code'         => CodeGenerator::employeeCode(),
                'name'                  => $request->namaLengkap,
                'phone'                 => $request->nomorHp,
                'address'               => $request->alamat,
                'avatar'                => $avatar,
                'gender'                => $request->jenisKelamin,
                'marital_status'        => $request->statusNikah,
                'skck'                  => $skck,
                'ktp'                   => $ktp,
                'employment_contract'   => $kontrak,
                'nip'                   => $request->nip,
                'birthdate'             => $request->tanggalLahir,
                'join_date'             => $request->tanggalBergabung
            ]);
        } catch (QueryException $e) {

            DB::rollBack();
            Log::warning($e->getMessage());
            throw $e;
        }

        DB::commit();

        return $employee;

    }

    public function update($id, $request)
    {
        $employee = Employee::auth()->with('user')->findOrFail($id);

        $avatar = $employee->avatar;
        if($request->hasFile('fotoKaryawan')) {
           $avatar = $avatar ?? Str::random(20).'.'.$request->fotoKaryawan->getClientOriginalExtension();
            $request->file('fotoKaryawan')->storeAs('foto-karyawan', $avatar, 'public');
        }

        $ktp = $employee->ktp;
        if($request->hasFile('fotoKtp')) {
            $ktp = $ktp ?? Str::random(20).'.'.$request->fotoKtp->getClientOriginalExtension();
            $request->file('fotoKtp')->storeAs('foto-ktp', $ktp, 'public');
        }

        $skck = $employee->skck;
        if($request->hasFile('fotoSkck')) {
            $skck = $skck ?? Str::random(20).'.'.$request->fotoSkck->getClientOriginalExtension();
            $request->file('fotoSkck')->storeAs('foto-skck', $skck, 'public');
        }

        $kontrak = $employee->employment_contract;
        if($request->hasFile('kontrakKerja')) {
            $kontrak = $kontrak ?? Str::random(20).'.'.$request->kontrakKerja->getClientOriginalExtension();
            $request->file('kontrakKerja')->storeAs('kontrak', $kontrak, 'public');
        }

        $company_id = (auth()->user()->user_type == 'admin') ? $request->perusahaan : auth()->user()->company_id;

        DB::beginTransaction();

        try {
            $employee->company_id           = $company_id;
            $employee->division_id          = $request->divisi;
            $employee->position_id          = $request->jabatan;
            $employee->employee_type_id     = $request->status;
            $employee->level_id             = $request->golongan;
            $employee->name                 = $request->namaLengkap;
            $employee->phone                = $request->nomorHp;
            $employee->address              = $request->alamat;
            $employee->avatar               = $avatar;
            $employee->gender               = $request->jenisKelamin;
            $employee->ktp                  = $ktp;
            $employee->skck                 = $skck;
            $employee->employment_contract  = $kontrak;
            $employee->marital_status       = $request->statusNikah;
            $employee->nip                  = $request->nip;
            $employee->birthdate            = $request->tanggalLahir;
            $employee->join_date            = $request->tanggalBergabung;
            $employee->user->username       = $request->username;
            $employee->user->password       = ($request->password) ? Hash::make($request->password) : $employee->user->password;
            $employee->push();

        } catch (QueryException $e) {

            DB::rollBack();
            Log::warning($e->getMessage());
            throw $e;
        }

        DB::commit();

        return $employee;
    }

    public function delete($id)
    {
        return Employee::auth()->where('id',$id)->delete();
    }

    public function checkUsername($username, $employee_id)
    {
        $user = User::where('username', $username)->whereHas('employee', function($query) use($employee_id){
            $query->where('id','!=',$employee_id);
        });

        return ($user) ? true : false;
    }

    public function getUserId($employee_id = 0)
    {
        $employee = Employee::find($employee_id);

        return ($employee) ? $employee->user_id : null;
    }
}