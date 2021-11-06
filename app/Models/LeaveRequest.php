<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    protected $table = 'leave_requests';
    protected $guarded = [];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function scopeAuth($query)
    {
        $user = auth()->user();
        $query->when($user->user_type == 'employee', function($sql) use($user){
            return $sql->where('employee_id', $user->employee->id);
        })
        ->when($user->user_type == 'company', function($sql) use($user){
            return $sql->whereHas('employee', function($q) use($user){
                $q->where('company_id', $user->company_id);
            });
        });
    }
}
