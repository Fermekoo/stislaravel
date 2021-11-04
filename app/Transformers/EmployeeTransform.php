<?php

namespace App\Transformers;

use App\Models\Employee;
use League\Fractal\TransformerAbstract;

class EmployeeTransform extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        //
    ];
    
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected $availableIncludes = [
        //
    ];
    
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Employee $employee)
    {
        return [
            'employeeCode'  => $employee->employee_code,
            'division'      => $employee->division->name,
            'position'      => $employee->position->name,
            'type'          => $employee->type->name,
            'level'         => $employee->level->name,
            'NIP'           => $employee->nip,
            'name'          => $employee->name,
            'phone'         => $employee->phone,
            'address'       => $employee->address,
            'gender'        => $employee->gender,
            'birthDate'     => $employee->birthdate,
            'status'        => $employee->status,
            'joinDate'      => $employee->join_date,
            'maritalStatus' => $employee->marital_status
        ];
    }
}
