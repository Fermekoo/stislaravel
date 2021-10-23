<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $table = 'employees';
    protected $guarded = [];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function division()
    {
        return $this->belongsTo(Division::class, 'division_id');
    }

    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id');
    }

    public function type()
    {
        return $this->belongsTo(EmployeeType::class, 'employee_type_id');
    }

    public function level()
    {
        return $this->belongsTo(EmployeeLevel::class, 'level_id');
    }

    public function leavequota()
    {
        return $this->hasMany(LeaveQuota::class, 'employee_id');
    }

    public function getAvailablequotaAttribute()
    {
        $leaves = $this->leavequota()->get();

        return ($leaves->isNotEmpty()) ? $leaves->sum('available_quota') : 0;
    }

    public function scopeAuth($query)
    {
        if (auth()->check()) {
            return $query->when(auth()->user()->user_type != 'admin', function ($q) {
                return $q->where('company_id', auth()->user()->company_id);
            });
        }
    }
}
