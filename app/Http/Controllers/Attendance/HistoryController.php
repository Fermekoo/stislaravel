<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Repositories\AttendanceRepo;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

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

    public function dataJson()
    {
        $attendances = $this->attendanceRepo->getAll();

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
}
