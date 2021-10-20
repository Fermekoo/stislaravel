<?php

namespace App\Http\Controllers\Leave;

use App\Http\Controllers\Controller;
use App\Repositories\LeaveRepo;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ApprovalController extends Controller
{
    protected $leaveRepo;
    public function __construct(LeaveRepo $leaveRepo)
    {
        $this->leaveRepo = $leaveRepo;
    }

    public function index()
    {
        return view('approval-leave.index');
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
                ->addColumn('leave', function($row){
                    return $row->type->name;
                })
                ->editColumn('start_leave', function($row){
                    return date_format(date_create($row->start_leave),'d-M-Y');
                })
                ->editColumn('end_leave', function($row){
                    return date_format(date_create($row->end_leave), 'd-M-Y');
                })
                ->toJson();

        return $data;
    }

    public function updateStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'leaveId' => 'required',
            'status'  => 'required|in:Reject,Accept'
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
