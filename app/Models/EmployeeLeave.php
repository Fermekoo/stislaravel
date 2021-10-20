<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeLeave extends Model
{
    protected $table = 'employee_leave';
    protected $guarded = [];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function type()
    {
        return $this->belongsTo(LeaveType::class, 'leave_type_id');
    }
    
}
