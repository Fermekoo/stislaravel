<?php

namespace App\Http\Controllers\Leave;

use App\Http\Controllers\Controller;
use App\Repositories\LeaveRequestRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ApprovalRequestController extends Controller
{
    protected $leaveRepo;
    public function __construct(LeaveRequestRepo $leaveRepo)
    {
        $this->leaveRepo = $leaveRepo;
    }

    public function index()
    {
        return view('approval-leave-request.index');
    }

    public function dataJson()
    {
        $leaves = $this->leaveRepo->getAll();

        $data = DataTables::of($leaves)
                ->addColumn('company', function($row){
                    return $row->employee->company->name;
                })
                ->addColumn('employee', function($row){
                    return $row->employee->name;
                })
                ->addColumn('employee_code', function($row){
                    return $row->employee->employee_code;
                })
                ->editColumn('start_leave', function($row){
                    return date_format(date_create($row->start_date),'d-M-Y');
                })
                ->editColumn('end_leave', function($row){
                    return date_format(date_create($row->end_date), 'd-M-Y');
                })
                ->toJson();

        return $data;
    }

    public function updateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'leaveId' => 'required',
            'status'  => 'required|in:Reject,Approve'
        ]);

        if($validator->fails()){
            $validation = $this->validatorMessage($validator);

            return $this->bad($validation['data'], 400);
        }

        try {
            $this->leaveRepo->updateStatus($request->leaveId, $request->status);
        } catch (\Exception $e) {
            return $this->bad('terjadi kesalahan', 500, $e->getMessage());
        }

        return $this->ok('data berhasil disimpan', 200);
    }
}
