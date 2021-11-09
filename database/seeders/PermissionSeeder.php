<?php

namespace Database\Seeders;

use App\Models\WebMenu;
use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{

    public function run()
    {
        $menus = array(
            [
                'menu'      => 'master',
                'name'      => 'Master',
                'childs'    => [
                    [
                        'menu'      => 'mst-perusahaan',
                        'name'      => 'Perusahaan',
                        'childs'    => []
                    ],
                    [
                        'menu'      => 'mst-divisi',
                        'name'      => 'Divisi',
                        'childs'    => []
                    ],
                    [
                        'menu'      => 'mst-jabatan',
                        'name'      => 'Venue',
                        'childs'    => []
                    ],
                    [
                        'menu'      => 'mst-jenis-cuti',
                        'name'      => 'Jenis Cuti',
                        'childs'    => []
                    ],
                    [
                        'menu'      => 'mst-status-karyawan',
                        'name'      => 'Status Karyawan',
                        'childs'    => []
                    ],
                    [
                        'menu'      => 'mst-golongan-karyawan',
                        'name'      => 'Golongan Karyawan',
                        'childs'    => []
                    ]
                ]
            ],
            [
                'menu'      => 'data-karyawan',
                'name'      => 'Data Karyawan',
                'childs'    => []
            ],
            [
                'menu'      => 'absen-cuti',
                'name'      => 'Absen dan Cuti',
                'childs'    => [
                    [
                        'menu'      => 'cuti',
                        'name'      => 'Cuti Karyawan',
                        'childs'    => []
                    ],
                    [
                        'menu'      => 'izin',
                        'name'      => 'Izin Karyawan',
                        'childs'    => []
                    ],
                    [
                        'menu'      => 'setting-absensi',
                        'name'      => 'Setting Absensi',
                        'childs'    => []
                    ],
                    [
                        'menu'      => 'absensi-karyawan',
                        'name'      => 'Absensi Karyawan',
                        'childs'    => []
                    ],
                    [
                        'menu'      => 'jatah-cuti',
                        'name'      => 'Jatah Cuti',
                        'childs'    => []
                    ]
                ]
            ],
            [
                'menu'      => 'user',
                'name'      => 'User',
                'childs'    => []
            ],
            [
                'menu'      => 'role',
                'name'      => 'Role',
                'childs'    => []
            ],
            [
                'menu'      => 'api-key',
                'name'      => 'API Key',
                'childs'    => []
            ],
        );

        $this->save($menus);
    }

    private function save($data_arr = [], $parent_id = null)
    {

        if(!empty($data_arr)) :
            foreach($data_arr as $data) {
               $menu = WebMenu::create([
                    'parent_id' => $parent_id,
                    'name'      => $data['name']
                ]);

                $permissions = (empty($data['childs'])) ? ['create', 'read','update','delete'] : ['read'];

                foreach($permissions as $permission) :
                    Permission::create([
                        'name'          => $data['menu'].'-'.$permission,
                        'guard_name'    => 'web',
                        'menu_id'       => $menu->id,
                        'type'        => $permission
                    ]);
                endforeach;

                if(!empty($data['childs'])) {
                    $this->save($data['childs'], $menu->id);
                }
            }
        endif;

        return true;
    }
}
