<?php 
namespace App\Repositories;

use App\Models\EmployeeType;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class EmployeeTypeRepo
{
    public function getAll()
    {
        return EmployeeType::auth()->has('company')->get();
    }

    public function updateOrCreate($request)
    {
        $employeeType = EmployeeType::auth()->find($request->employeeTypeId);

        try {

            if($employeeType) {
                $employeeType->company_id = (auth()->user()->user_type == 'admin') ? $request->companyId : $employeeType->company_id;
                $employeeType->name       = $request->tipeKaryawan;
                $employeeType->save();

            } else {
                EmployeeType::create([
                    'company_id'    => (auth()->user()->user_type == 'admin') ? $request->companyId : auth()->user()->company_id,
                    'name'          => $request->tipeKaryawan,
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
        return EmployeeType::auth()->findOrFail($id);
    }

    public function delete($id)
    {
        return EmployeeType::auth()->where('id',$id)->delete();
    }
}