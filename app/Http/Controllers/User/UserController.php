<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Repositories\RoleRepo;
use App\Repositories\UserRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    protected $userRepo, $roleRepo;
    public function __construct(UserRepo $userRepo, RoleRepo $roleRepo)
    {
        $this->userRepo = $userRepo;
        $this->roleRepo = $roleRepo;
    }

    public function index()
    {

        $roles = $this->roleRepo->getAll();
        return view('user.index', compact('roles'));
    }

    public function dataJson()
    {
        $users = $this->userRepo->getAll();

        $data = DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('role', function($row){
                    return ($row->role->isNotEmpty()) ? $row->role[0]->display : '';
                })
                ->addColumn('roleid', function($row){
                    return ($row->role->isNotEmpty()) ? $row->role[0]->id : '';
                })
                ->toJson();
        return $data;
    }

    public function updateOrCreate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userId'    => 'nullable',
            'username'  => 'required|unique:users,username,'.$request->userId,
            'email'     => 'nullable|email|unique:users,email,'.$request->userId,
            'password'  => 'nullable|min:6',
            'role'      => 'required|exists:roles,name'
        ]);

        if($validator->fails()){
            $validation = $this->validatorMessage($validator);

            return $this->bad($validation['data'], 400);
        }

        try {

            ($request->userId) ? $this->userRepo->update($request->userId, $request) : $this->userRepo->create($request);

        } catch (\Exception $e) {
            return $this->bad('terjadi kesalahan', 500, $e->getMessage());
        }

        return $this->ok('data berhasil disimpan', 200);

    }

    public function detail($id)
    {
        return $this->ok('detail', 200, $this->userRepo->findById($id));
    }

    public function delete($id)
    {
        try {

            $this->userRepo->delete($id);

        } catch (\Exception $e) {
            return $this->bad('terjadi kesalahan', 500, $e->getMessage());
        }

        return $this->ok('data berhasil dihapus', 200);
    }

    public function assignRoleToEmployee(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'modelId'   => 'nullable',
            'roleId'    => 'nullable',
            'userId'    => 'required',
            'role'      => 'required|exists:roles,name'
        ]);

        if($validator->fails()){
            $validation = $this->validatorMessage($validator);

            return $this->bad($validation['data'], 400);
        }

        try {

            $this->userRepo->assignEmployeRole($request);

        } catch (\Exception $e) {
            return $this->bad('terjadi kesalahan', 500, $e->getMessage());
        }

        return $this->ok('data berhasil disimpan', 200);
    }

    public function deleteEmployeeRole($model_id, $role_id)
    {
        try {

            $this->userRepo->deleteEmployeeRole($model_id, $role_id);

        } catch (\Exception $e) {
            return $this->bad('terjadi kesalahan', 500, $e->getMessage());
        }

        return $this->ok('data berhasil dihapus', 200);
    }
}
