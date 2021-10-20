<?php
namespace App\Repositories;

use App\Models\Employee;
use App\Models\EmployeeLeave;
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

    public function checkQuota($employee_id, $leave_type_id, $duration)
    {
        $quota = LeaveQuota::where('leave_type_id', $leave_type_id)->where('employee_id', $employee_id)->first();

        return ($quota) ? ($quota->available_quota >= $duration) ? true : false : false;
    }

    public function requestLeave($employee_id, $duration, $request)
    {
        try {
            return EmployeeLeave::create([
                'employee_id'   => $employee_id,
                'leave_type_id' => $request->jenisCuti,
                'start_leave'   => $request->tanggalMulaiCuti,
                'end_leave'     => $request->tanggalSelesaiCuti,
                'duration'      => $duration,
                'description'   => $request->keterangan,
                'status'        => 'Request',
                'updated_by'    => auth()->user()->id    
            ]);
        } catch (QueryException $e) {
            Log::warning($e->getMessage());
            throw $e;
        } 
    }

    public function updateRequestLeave($id, $employee_id, $duration, $request)
    {
        $leave = EmployeeLeave::where([
            'id'            => $id,
            'employee_id'   => $employee_id,
            'status'        => 'Request'
        ])->first();

        if($leave) :
            $leave->leave_type_id = $request->jenisCuti;
            $leave->start_leave   = $request->tanggalMulaiCuti;
            $leave->end_leave     = $request->tanggalSelesaiCuti;
            $leave->duration      = $duration;
            $leave->description   = $request->keterangan;
            $leave->save();
        endif;
    }

    public function isAvailableToRequest($employee_id)
    {
        $leave = EmployeeLeave::where([
            'employee_id'   => $employee_id,
            'status'        => 'Request'
        ])->first();

        return ($leave) ? false : true;
    }

    public function getAllEmployeLeave($employee_id)
    {
        return EmployeeLeave::where('employee_id', $employee_id)->get();
    }

    public function findById($id, $employee_id)
    {
        $leave = EmployeeLeave::where([
            'employee_id' => $employee_id,
            'id'          => $id
        ])->first();

        return $leave;
    }

    public function delete($id, $employee_id)
    {
        $leave = EmployeeLeave::where([
            'employee_id' => $employee_id,
            'id'          => $id
        ])->delete();

        return $leave;
    }

    public function getAll()
    {
        $leaves = EmployeeLeave::with(
                'employee:id,company_id,name,employee_code',
                'employee.company:id,name',
                'type:id,name'
            )
            ->when(auth()->user()->user_type != 'admin', function($q){
                return $q->whereHas('employee', function($query){
                    $query->where('company_id', auth()->user()->company_id);
                });
            })
            ->get();

        return $leaves;
    }

    public function updateStatus($id, $status)
    {
        $leave = EmployeeLeave::when(auth()->user()->user_type != 'admin', function($q){
            return $q->whereHas('employee', function($query){
                $query->where('company_id', auth()->user()->company_id);
            });
        })
        ->findOrFail($id);

        DB::beginTransaction();
        try {
            $leave->status      = $status;
            $leave->updated_by  = auth()->user()->id;
            $leave->save();

        } catch (QueryException $e) {
            Log::warning($e->getMessage());
            DB::rollBack();
            throw $e;
        } 

        if($status == 'Accept') :
            try {
                $quota = LeaveQuota::where('employee_id', $leave->employee_id)->where('leave_type_id', $leave->leave_type_id)->first();
                $quota->increment('used_quota', $leave->duration);
                $quota->decrement('available_quota', $leave->duration);
            } catch (QueryException $e) {
                Log::warning($e->getMessage());
                DB::rollBack();
                throw $e;
            }
        endif;

        DB::commit();

        return true;
    }

}