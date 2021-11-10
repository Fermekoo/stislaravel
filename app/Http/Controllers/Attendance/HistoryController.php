<?php

namespace App\Http\Controllers\Attendance;

use App\Exports\AttendanceExport;
use App\Http\Controllers\Controller;
use App\Repositories\AttendanceRepo;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;

class HistoryController extends Controller
{
    protected $attendanceRepo;
    public function __construct(AttendanceRepo $attendanceRepo)
    {
        $this->attendanceRepo = $attendanceRepo;
    }

    public function index()
    {
        return view('history-attendance.index');
    }

    public function dataJson(Request $request)
    {
        $attendances = $this->attendanceRepo->getAll($request);

        $data = DataTables::of($attendances)
                ->addColumn('employee',function($row){
                    return $row->employee->name;
                })
                ->addColumn('company',function($row){
                    return $row->employee->company->name;
                })
                ->addColumn('date',function($row){
                    return date_format(date_create($row->check_in), 'd-M-Y');
                })
                ->editColumn('check_in', function($row){
                    if($row->attendance_type == 'absensi') {
                        return date_format(date_create($row->check_in), 'H:i:s');
                    } else {
                        return '-';
                    }
                })
                ->editColumn('check_out', function($row){
                    if($row->attendance_type == 'absensi') {
                        return date_format(date_create($row->check_out), 'H:i:s');
                    } else {
                        return '-';
                    }
                })
                ->addColumn('check_in_status', function($row){

                    if($row->attendance_type == 'absensi') {
                        return ($row->is_late_attendance) ? 'Terlambat' : 'Tepat Waktu';
                    } else if ($row->attendance_type == 'izin') {
                        return $row->izin->request_type;
                    } else {
                        return $row->leave->type->name;
                    }
                })

                ->addColumn('check_out_status', function($row){

                    if($row->attendance_type == 'absensi') {
                        return ($row->is_early_checkout) ? 'Pulang Cepat' : 'Tepat Waktu';
                    } else if ($row->attendance_type == 'izin') {
                        return $row->izin->request_type;
                    } else {
                        return $row->leave->type->name;
                    }
                })
                ->toJson();

        return $data;
    }

    public function toExcel(Request $request)
    {   
       
        $attendances = $this->attendanceRepo->getAll($request);
        $attendances = $attendances->map(function($row){

            if($row->attendance_type == 'absensi') {
                $check_in_status = ($row->is_late_attendance) ? 'Terlambat' : 'Tepat Waktu';
                $check_out_status = ($row->is_early_checkout) ? 'Pulang Cepat' : 'Tepat Waktu';
            } else if ($row->attendance_type == 'izin') {
                $check_in_status = $row->izin->request_type;
                $check_out_status = $row->izin->request_type;
            } else {
                $check_in_status = $row->leave->type->name;
                $check_out_status = $row->leave->type->name;
            }

            return (object)[
                'employee_code'     => $row->employee->employee_code,
                'company'           => $row->employee->company->name,
                'name'              => $row->employee->name,
                'date'              => date_format(date_create($row->check_in), 'd-M-Y'),
                'check_in'          => ($row->attendance_type == 'absensi') ? date_format(date_create($row->check_in), 'H:i:s') : '-',
                'check_out'         => ($row->attendance_type == 'absensi') ? date_format(date_create($row->check_out), 'H:i:s') : '-',
                'checkin_status'    => $check_in_status,
                'checkout_status'   => $check_out_status
            ];
        });

        return Excel::download(new AttendanceExport($attendances), 'laporan_absensi.xlsx');
    }
}
