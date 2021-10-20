<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Repositories\AttendanceRepo;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    protected $attendanceRepo;
    public function __construct(AttendanceRepo $attendanceRepo)
    {
        $this->attendanceRepo = $attendanceRepo;
    }
    public function index()
    {
        $time_config = $this->attendanceRepo->getTimeConfig(auth()->user()->company_id);
        $attendance  = $this->attendanceRepo->getDailyAttendance(auth()->user()->employee->id);

        return view('attendance.index', compact('time_config', 'attendance'));
    }

    public function setAttendance(Request $request)
    {
        try {

            $this->attendanceRepo->updateAttendance(auth()->user()->employee->id, auth()->user()->company_id, $request);

        } catch (\Exception $e) {

            return redirect()->back()->with('error','Terjadi kesalahan server');
        }

        return redirect()->back()->with('success','Data berhasil disimpan');
    }
}
