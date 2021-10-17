<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebMenu extends Model
{
    protected $table    = 'web_menus';
    protected $guarded  = [];

    public function childs()
    {
        return $this->hasMany(WebMenu::class, 'parent_id','id');
    }

    public function parent()
    {
        return $this->belongsTo(WebMenu::class, 'parent_id','id');
    }

    public function permissions()
    {
        return $this->hasMany(Permission::class, 'menu_id','id')->orderByRaw("FIELD(type, 'read','create','update','delete')");
    }


}
