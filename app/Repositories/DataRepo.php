<?php
namespace App\Repositories;

use App\Models\Division;
use App\Models\EmployeeLevel;
use App\Models\EmployeeType;
use App\Models\LeaveType;
use App\Models\Position;
use App\Models\Role;

class DataRepo
{
    public function division($company_id)
    {
        return Division::where('company_id', $company_id)->get();
    }

    public function position($company_id)
    {
        return Position::where('company_id', $company_id)->get();
    }

    public function employeeLevel($company_id)
    {
        return EmployeeLevel::where('company_id', $company_id)->get();
    }

    public function employeeType($company_id)
    {
        return EmployeeType::where('company_id', $company_id)->get();
    }

    public function leaveType($company_id)
    {
        return LeaveType::where('company_id', $company_id)->get();
    }

    public function roles($company_id)
    {
        return Role::where('company_id', $company_id);
    }
}