<?php

namespace App\Http\Controllers\Role;

use App\Http\Controllers\Controller;
use App\Repositories\PermissionRepo;
use App\Repositories\RoleRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    protected $roleRepo;
    protected $permissionRepo;
    public function __construct(RoleRepo $roleRepo, PermissionRepo $permissionRepo)
    {
        $this->roleRepo = $roleRepo;
        $this->permissionRepo = $permissionRepo;
    }

    public function index()
    {
        return view('role.list');
    }

    public function dataJson()
    {
        return DataTables::of($this->roleRepo->getAll())
                ->addIndexColumn()
                ->editColumn('name', function($row){
                    return explode('#',$row->name)[1];
                })
                ->addColumn('action', function($row){
                    return route('roles.edit', $row->id);
                })
                ->addColumn('is_deleted', function($row){
                    return in_array($row->name, ['ROOT']) ? false : true;
                })
                ->toJson();
    }

    public function create()
    {
        $menus = $this->permissionRepo->getModules();

        return view('role.create', compact('menus'));
    }

    public function store(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'role_name'     => 'required|min:2|unique:roles,name',
            'permissions'   => 'required|array',
            'permissions.*' => 'required|exists:permissions,name'
        ]);

        if($validator->fails()) {
            return back()->withErrors($validator)->withInput($request->all())->with('error', $validator->getMessageBag()->first());
        }

        try {

            $this->roleRepo->create($request, auth()->user()->company_id);

        } catch (\Exception $e) {
            return back()->withErrors($validator)->withInput($request->all())->with('error', $e->getMessage());
        }

        return redirect()->route('roles')->with('success', 'Berhasil menyimpan data role');
    }

    public function edit($id)
    {
        $modules = $this->roleRepo->onEditPage($id);

        if(!$modules) return redirect()->route('roles')->with('error','data tidak ditemukan');


        return view('role.edit', compact('modules'));
    }

    public function update($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role_name'     => 'required|min:2|unique:roles,name,'.$id,
            'permissions'   => 'required|array',
            'permissions.*' => 'required|exists:permissions,name'
        ]);

        if($validator->fails()) {
            return back()->withErrors($validator)->withInput($request->all())->with('error', $validator->getMessageBag()->first());
        }

        try {

            $this->roleRepo->update($id, $request, auth()->user()->company_id);

        } catch (\Exception $e) {
            return back()->withErrors($validator)->withInput($request->all())->with('error', $e->getMessage());
        }

        return redirect()->route('roles')->with('success', 'Berhasil mengubah data role');
    }

    public function delete($id)
    {
        try {

            $this->roleRepo->delete($id);

        } catch (\Exception $e) {
			return $this->bad($e->getMessage(), 409, $e->getMessage());
		}

        return $this->ok("Berhasil menghapus data role", 200, null);
    }
}
