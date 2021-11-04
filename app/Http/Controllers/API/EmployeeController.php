<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Repositories\EmployeeRepo;
use App\Transformers\EmployeeTransform;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    protected $employeeRepo;
    public function __construct(EmployeeRepo $employeeRepo)
    {
        $this->employeeRepo = $employeeRepo;
    }

    public function getAll()
    {
        $employees = $this->employeeRepo->getAll();

        $data = fractal()
                ->collection($employees)
                ->transformWith(new EmployeeTransform)
                ->toArray();

        return $this->ok('employees', 200, $data);
    }
}
