<?php 
namespace App\Repositories;

use App\Models\EmployeeLevel;
use App\Support\CodeGenerator;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class EmployeeLevelRepo
{
    public function getAll()
    {
        return EmployeeLevel::auth()->has('company')->get();
    }

    public function updateOrCreate($request)
    {
        $employeeLevel = EmployeeLevel::auth()->find($request->employeeLevelId);

        try {

            if($employeeLevel) {
                $employeeLevel->company_id = (auth()->user()->user_type == 'admin') ? $request->companyId : $employeeLevel->company_id;
                $employeeLevel->name       = $request->golongan;
                $employeeLevel->save();

            } else {
                EmployeeLevel::create([
                    'company_id'    => (auth()->user()->user_type == 'admin') ? $request->companyId : auth()->user()->company_id,
                    'name'          => $request->golongan,
                ]);
            }
            
        } catch (QueryException $e) {
            Log::warning($e->getMessage());
            throw $e;
        }
        
        return true;
    }

    public function findById($id)
    {
        return EmployeeLevel::auth()->findOrFail($id);
    }

    public function delete($id)
    {
        return EmployeeLevel::auth()->where('id',$id)->delete();
    }
}