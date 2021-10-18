<?php 
namespace App\Repositories;

use App\Models\LeaveType;
use App\Support\CodeGenerator;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class LeaveTypeRepo
{
    public function getAll()
    {
        return LeaveType::auth()->has('company')->get();
    }

    public function updateOrCreate($request)
    {
        $leaveType = LeaveType::auth()->find($request->leaveTypeId);

        try {

            if($leaveType) {
                $leaveType->company_id = (auth()->user()->user_type == 'admin') ? $request->companyId : $leaveType->company_id;
                $leaveType->name       = $request->tipeCuti;
                $leaveType->save();

            } else {
                LeaveType::create([
                    'company_id'    => (auth()->user()->user_type == 'admin') ? $request->companyId : auth()->user()->company_id,
                    'name'          => $request->tipeCuti,
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
        return LeaveType::auth()->findOrFail($id);
    }

    public function delete($id)
    {
        return LeaveType::auth()->where('id',$id)->delete();
    }
}