<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceTimeConfig extends Model
{
    protected $table   = 'attendance_time_configs';
    protected $guarded = [];

    public function company()
    {
        return $this->belongsTo(company::class, 'company_id');
    }

    public function scopeAuth($query)
    {
        if(auth()->check()) {
            return $query->when(auth()->user()->user_type != 'admin', function($q){
                return $q->where('company_id', auth()->user()->company_id);
            });
        }
    }
}
