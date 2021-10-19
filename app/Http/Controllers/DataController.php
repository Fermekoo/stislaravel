<?php

namespace App\Http\Controllers;

use App\Repositories\DataRepo;
use App\Repositories\EmployeeRepo;
use Illuminate\Http\Request;

class DataController extends Controller
{
    protected $dataRepo, $employeeRepo;
    public function __construct(DataRepo $dataRepo, EmployeeRepo $employeeRepo)
    {
        $this->dataRepo = $dataRepo;
        $this->employeeRepo = $employeeRepo;
    }

    public function division($company_id)
    {
        return $this->ok('division', 200, $this->dataRepo->division($company_id));
    }
    
    public function position($company_id)
    {
        return $this->ok('division', 200, $this->dataRepo->position($company_id));
    }

    public function employeeLevel($company_id)
    {
        return $this->ok('division', 200, $this->dataRepo->employeeLevel($company_id));
    }

    public function employeeType($company_id)
    {
        return $this->ok('division', 200, $this->dataRepo->employeeType($company_id));
    }
    
    public function leaveType($company_id)
    {
        return $this->ok('division', 200, $this->dataRepo->leaveType($company_id));
    }

    public function employeesNoRole()
    {
        $employees = $this->employeeRepo->getEmployeeNoRole()->map(function($row){
            return $row->employee;
        });
        return $this->ok('employee', 200, $employees);
    }

}
