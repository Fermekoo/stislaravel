<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'employee_attendances';
    protected $guarded = [];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function leave()
    {
        return $this->belongsTo(EmployeeLeave::class, 'leave_id');
    }

    public function izin()
    {
        return $this->belongsTo(LeaveRequest::class, 'leave_id');
    }
}
