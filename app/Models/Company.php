<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $table = 'companies';
    protected $guarded = [];

    public function admin()
    {
        return $this->hasOne(User::class, 'company_id')->where('user_type', 'company');
    }

    public function keys()
    {
        return $this->hasMany(APIKey::class, 'company_id');
    }

    public function timeattendance()
    {
        return $this->hasOne(AttendanceTimeConfig::class, 'company_id');
    }
}
