<?php

namespace App\Http\Controllers\Leave;

use App\Http\Controllers\Controller;
use App\Repositories\LeaveRepo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class RequestController extends Controller
{
    protected $leaveReRepo;
    public function __construct(LeaveRepo $leaveReRepo)
    {
        $this->leaveReRepo = $leaveReRepo;
    }

    public function index()
    {
        $leave_quota = $this->leaveReRepo->getQuota(auth()->user()->employee->id);
        return view('request-leave.index', compact('leave_quota'));
    }

    public function dataJson()
    {
        $employee_id = auth()->user()->employee->id;
        $leaves = $this->leaveReRepo->getAllEmployeLeave($employee_id);

        $data = DataTables::of($leaves)
                ->addIndexColumn()
                ->editColumn('start_leave', function($row){
                    return date_format(date_create($row->start_leave), 'd-M-Y');
                })
                ->editColumn('end_leave', function($row){
                    return date_format(date_create($row->end_leave), 'd-M-Y');
                })
                ->addColumn('leave', function($row){
                    return $row->type->name;
                })
                ->toJson();

        return $data;
    }

    public function detail($id)
    {
        $employee_id = auth()->user()->employee->id;
        $leave = $this->leaveReRepo->findById($id, $employee_id);

        return $this->ok('detail', 200, $leave);
    }

    public function updateOrCreate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cutiId'                => 'nullable',
            'jenisCuti'             => 'required',
            'tanggalMulaiCuti'      => 'required|date_format:Y-m-d|after_or_equal:'.date('Y-m-d'),
            'tanggalSelesaiCuti'    => 'required|after:tanggalMulaiCuti',
            'keterangan'            => 'nullable'
        ],[
            'tanggalMulaiCuti.after_or_equal' => 'tanggal mulai cuti harus besar atau sama dengan hari ini'
        ]);

        if($validator->fails()){
            $validation = $this->validatorMessage($validator);

            return $this->bad($validation['data'], 400);
        }
        
        $duration      = Carbon::parse($request->tanggalMulaiCuti)->diffInDays(Carbon::parse($request->tanggalSelesaiCuti)) + 1;
        $employee_id = auth()->user()->employee->id;

        $check_quota = $this->leaveReRepo->checkQuota($employee_id, $request->jenisCuti, $duration);

        if(!$check_quota) :
            return $this->bad(['durasi' => 'jatah cuti tidak mencukupi'], 400);
        endif;

        try {

            if($request->cutiId) {

                $this->leaveReRepo->updateRequestLeave($request->cutiId, $employee_id, $duration, $request);
            } else {

                $check_request = $this->leaveReRepo->isAvailableToRequest($employee_id);

                if(!$check_request) :
                    return $this->bad(['durasi' => 'anda masih memiliki permintaan cuti yang belum diproses'], 400);
                endif;

                $this->leaveReRepo->requestLeave($employee_id, $duration, $request);
            } 

        } catch (\Exception $e) {
            return $this->bad('terjadi kesalahan', 500, $e->getMessage());
        }

        return $this->ok('data berhasil disimpan', 200);
    }

    public function delete($id)
    {
        $employee_id = auth()->user()->employee->id;
        try {
            $this->leaveReRepo->delete($id, $employee_id);
        } catch (\Exception $e) {
            return $this->bad('terjadi kesalahan', 500, $e->getMessage());
        }

        return $this->ok('data berhasil dihapus', 200);
    }
}
