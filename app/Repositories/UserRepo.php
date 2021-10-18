<?php
namespace App\Repositories;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Exceptions\RoleDoesNotExist;

class UserRepo
{
    public function getAll()
    {
        return User::auth()->whereHas('role', function($query){
            $query->where('name','!=','ROOT');
        })->get();
    }

    public function create($request)
    {
        DB::beginTransaction();
        try {
            $user = User::create([
                'username'  => $request->username,
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'user_type' => 'admin'
            ]);
        } catch (QueryException $e) {
            Log::warning($e->getMessage());
            DB::rollBack();
            throw $e;
        }

        try {

            $user->assignRole($request->role);

        } catch (QueryException $e) {
            Log::warning($e->getMessage());
            DB::rollBack();
            throw $e;
        } catch (RoleDoesNotExist $e) {
            Log::warning($e->getMessage());
            DB::rollBack();
            throw $e;
        }

        DB::commit();

        return $user;
    }

    public function update($id, $request)
    {
        $user = User::auth()->findOrFail($id);

        DB::beginTransaction();

        try {
            $user->username = $request->username;
            $user->email    = $request->email ?? $user->email;
            $user->password = ($request->password) ? Hash::make($request->password) : $user->password;
            $user->save();

        } catch (QueryException $e) {
            Log::warning($e->getMessage());
            DB::rollBack();
            throw $e;
        }

        try {
            $user->syncRoles($request->role);
        } catch (QueryException $e) {
            Log::warning($e->getMessage());
            DB::rollBack();
            throw $e;
        } catch (RoleDoesNotExist $e) {
            Log::warning($e->getMessage());
            DB::rollBack();
            throw $e;
        }
        DB::commit();
        return $user;
    }

    public function findById($id)
    {
        $user = User::auth()->find($id);
        $user->rolename = ($user->role->isNotEmpty()) ? $user->role[0]->name : '';
        $user->roleid = ($user->role->isNotEmpty()) ? $user->role[0]->id : '';

        return $user;
    }

    public function delete($id)
    {
        return User::auth()->where('id', $id)->where('user_type','!=','company')->delete();
    }

    public function assignEmployeRole($request)
    {
        $user = User::auth()->where('user_type', 'employee')->where('id', $request->userId)->first();
        if($user) {
            try {

                $user->assignRole($request->role);

            } catch (RoleDoesNotExist $e) {
                throw $e;
            }
        }

        return true;
    }

    public function switchEmployeeRole($request)
    {
        $role = Role::where('name', $request->role)->first();

        $role_user = DB::table('model_has_roles')->where('role_id', $request->roleId)->where('model_id', $request->modelId)->update([
            'role_id'  => $role->id,
            'model_id' => $role->modelId
        ]);

        return $role_user;

    }

    public function deleteEmployeeRole($model_id, $role_id)
    {
        return DB::table('model_has_roles')->where('role_id', $role_id)->where('model_id', $model_id)->delete();
    }
}