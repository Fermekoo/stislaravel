<?php
namespace App\Repositories;

use App\Models\Employee;
use App\Models\LeaveQuota;
use App\Models\LeaveType;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class LeaveRepo
{
    public function setQuota($employee_id, $request)
    {
        $employee = Employee::auth()->findOrFail($employee_id);

        DB::beginTransaction();

        foreach($request->quota as $quota) : 
           try {
            $leave_quota = LeaveQuota::where('leave_type_id', $quota['leave_type_id'])->where('employee_id', $employee->id)->first();
            if($leave_quota) {
                $leave_quota->quota             = $leave_quota->quota + ($quota['qty'] - $leave_quota->available_quota);
                $leave_quota->available_quota   = $quota['qty'];
                $leave_quota->save();
            } else {
                LeaveQuota::create([
                    'employee_id'       => $employee->id,
                    'leave_type_id'     => $quota['leave_type_id'],
                    'quota'             => $quota['qty'],
                    'available_quota'   => $quota['qty']
                ]);
            }
           } catch (QueryException $e) {
               DB::rollBack();
               Log::warning($e->getMessage());
               throw $e;
           } catch (\Exception $e){
                DB::rollBack();
                Log::warning($e->getMessage());
                throw $e;
           }
        endforeach;

        DB::commit();
        return true;
       
    }

    public function getQuota($employee_id)
    {
        $employee = Employee::auth()->findOrFail($employee_id);
        $company_id = $employee->company_id;
        
        $leave_types = LeaveType::auth()->where('company_id', $company_id)->get();

        $employee_leaves = [];

        foreach($leave_types as $type) : 
            $quota = LeaveQuota::where('leave_type_id', $type->id)->where('employee_id', $employee_id)->first();
            $employee_leaves[] = [
                'leave_type_id'     => $type->id,
                'leave_type'        => $type->name,
                'available_quota'   => ($quota) ? $quota->available_quota : 0
            ];
        endforeach;

        return $employee_leaves;
    }
}