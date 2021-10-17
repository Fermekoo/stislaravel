<?php 
namespace App\Repositories;

use App\Models\Permission;
use App\Models\WebMenu;

class PermissionRepo
{
    protected $permission;
    public function __construct(Permission $permission)
    {
        $this->permission = $permission;
    }

    public function getAll() 
    {
        return $this->permission->get();
    }

    public function groupByMenu()
    {
        
        $menu = $this->permission->groupBy('parent_menu')->get()->map(function($row) {
            return [
                'menu_name'     => $row->parent_menu,
                'permissions'   => $this->permission->select('id','name','parent_menu')->where('parent_menu',$row->parent_menu)->get()
            ];
        });

        return $menu;
    }
    
    public function getModules()
    {
        $module = WebMenu::with('childs','permissions','childs.permissions')->whereNull('parent_id')->get();

        return $module;
    }

}