<?php 
namespace App\Repositories;

use Illuminate\Support\Facades\DB;
use App\Models\Role;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Exceptions\PermissionDoesNotExist;
use App\Repositories\PermissionRepo;
use App\Models\Company;

class RoleRepo
{
    protected $role, $permissionRepo;

    public function __construct(Role $role, PermissionRepo $permissionRepo)
    {
        $this->role = $role;
        $this->permissionRepo = $permissionRepo;
    }

    public function getAll()
    {
        return $this->role->auth()->where('name','!=','ROOT')->withCount('permissions')->get();
    }

    public function create($payloads, $company_id)
    {
        $company = Company::select('company_code')->find($company_id);

        $role_name = ($company) ? $company->company_code.'#'.str_replace('#','',$payloads->role_name) : str_replace('#','',$payloads->role_name);

        DB::beginTransaction();

        try {

            $role = $this->role->create([
                'name'          => $role_name,
                'company_id'    => $company_id,
                'guard_name'    => 'web'
            ]);

        } catch (QueryException $e) {
            Log::warning($e->getMessage());
            DB::rollback();
            throw $e;
        }

        try {

            $role->syncPermissions(array_unique($payloads->permissions));

        } catch (PermissionDoesNotExist $e) {
            Log::warning($e->getMessage());
            DB::rollback();
            throw $e;

        } catch (QueryException $e) {
            Log::warning($e->getMessage());
            DB::rollback();
            throw $e;
        }

        DB::commit();

        return $role;
    }

    public function getDetail($id)
    {
        
        return $this->role->auth()->find($id);
    }

    public function update($id, $payloads, $company_id)
    {
        $company = Company::select('company_code')->find($company_id);

        $role_name = ($company) ? $company->company_code.'#'.str_replace('#','',$payloads->role_name) : str_replace('#','',$payloads->role_name);
        DB::beginTransaction();

        try {

            $role = $this->role->auth()->findOrFail($id);
            
            $role->name = (in_array($role->name, ['ROOT'])) ? $role->name : $role_name;
            $role->save();

        } catch (QueryException $e) {
            Log::warning($e->getMessage());
            DB::rollback();
            throw $e;
        }

        try {

            $role->syncPermissions(array_unique($payloads->permissions));

        } catch (PermissionDoesNotExist $e) {
            Log::warning($e->getMessage());
            DB::rollback();
            throw $e;

        } catch (QueryException $e) {
            Log::warning($e->getMessage());
            DB::rollback();
            throw $e;
        }

        DB::commit();

        return $role;
    }

    public function delete($id)
    {
        $delete = $this->role->auth()->where('id',$id)->first();

        if(in_array($delete->name, ['ROOT'])){
            throw new \Exception('Role default tidak bisa di hapus');
        }
        
        return $delete->delete();
    }

    public function onEditPage($id)
    {
       
        $role = $this->getDetail($id);
        if(!$role) return false;
        $role_permissions = $role->permissionNames;

        $modules = $this->permissionRepo->getModules();
        $new_modules = [];
        foreach($modules as $module) : 
            $childs = $module->childs;
            $module_permissions = $module->permissions;
            $module_permissions_count = $module_permissions->count();
            $new_modules[] = (object)[
                'id'                    => $module->id,
                'menu_name'             => $module->name,
                'count_childs'          => $childs->count(),
                'count_permissions'     => $module_permissions_count,
                'read'                  => $module_permissions[0]->name,
                'read_checked'          => (in_array($module->permissions[0]->name, $role->permissionNames)) ? 'checked' : '',
                'permissions'           => ($module_permissions_count > 1) ? $module_permissions->map(function($mp) use($role_permissions){
                                            return (object)[
                                                'name'          => $mp->name,
                                                'id'            => $mp->id,
                                                'is_checked'    => (in_array($mp->name, $role_permissions)) ? 'checked' : ''

                                            ];
                                        }) : null,
                'childs'                => $childs->map(function($child) use($role_permissions){
                                                return (object)[
                                                    'id'            => $child->id,
                                                    'menu_name'     => $child->name,
                                                    'is_checked'    => (!empty(array_intersect($child->permissions->map(function($item){return $item->name;})->toArray(), $role_permissions))) ? 'checked' : '',
                                                    'permissions'    => $child->permissions->map(function($cp) use($role_permissions){
                                                        return (object)[
                                                            'name'          => $cp->name,
                                                            'is_checked'    => (in_array($cp->name, $role_permissions)) ? 'checked' : ''
                                                        ];
                                                    }),
                                                ];
                                            }),
            ];
        endforeach;

        return [
            'id'    => $role->id,
            'role'  => $role->name,
            'menus' => $new_modules
        ];
    }

}