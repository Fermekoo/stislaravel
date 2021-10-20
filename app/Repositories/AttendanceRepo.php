<?php 
namespace App\Repositories;

use Carbon\Carbon;
use App\Models\Attendance;
use App\Models\AttendanceTimeConfig;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class AttendanceRepo
{
    public function updateTimeConfig($company_id, $request)
    {
        try {
           $config = AttendanceTimeConfig::where('company_id', $company_id)->first();
           $config->check_in        = $request->jamMasuk;
           $config->limit_check_in  = $request->batasJamMasuk;
           $config->check_out       = $request->jamPulang;
           $config->save();
        } catch (QueryException $e) {
            Log::warning($e->getMessage());
            throw $e;
        }

        return $config;
    }

    public function getTimeConfig($company_id)
    {
        return AttendanceTimeConfig::where('company_id', $company_id)->first();
    }

    public function setAttendance($employee_id, $company_id, $request)
    {
        $time_config    = $this->getTimeConfig($company_id);
        $base_check_in  = Carbon::parse(date('Y-m-d').' '.$time_config->check_in);
        $now            = Carbon::now();
        $is_late        = $now->gt($base_check_in);

        try {

         return  Attendance::create([
                'employee_id'           => $employee_id,
                'check_in'              => $now,
                'is_late_attendance'    => $is_late,
                'longitude'             => $request->longitude,
                'latitude'              => $request->latitude
            ]);

        } catch (QueryException $e) {
            throw $e;
        }
    }

    public function updateAttendance($employee_id, $company_id, $request)
    {
        $time_config    = $this->getTimeConfig($company_id);
        $now            = Carbon::now();
        $attendance = Attendance::where('employee_id', $employee_id)->whereDate('check_in', date('Y-m-d'))->first();

        if($attendance) {
            try {
                $base_check_out = Carbon::parse(date('Y-m-d').' '.$time_config->check_out);
                $is_early       = $now->lt($base_check_out);

                $attendance->check_out          = $now;
                $attendance->is_early_checkout  = $is_early;
                $attendance->lat_checkout       = $request->latitude;
                $attendance->long_checkout      = $request->longitude;
                $attendance->save();
            } catch (QueryException $e) {
                Log::warning($e->getMessage());
                throw $e;
            
            } catch (\Exception $e) {
                Log::warning($e->getMessage());
                throw $e;
            }
        } else {
            $base_check_in  = Carbon::parse(date('Y-m-d').' '.$time_config->check_in);
            $is_late        = $now->gt($base_check_in);

            try {

            return  Attendance::create([
                    'employee_id'           => $employee_id,
                    'check_in'              => $now,
                    'is_late_attendance'    => $is_late,
                    'longitude'             => $request->longitude,
                    'latitude'              => $request->latitude
                ]);

            } catch (QueryException $e) {
                throw $e;
            }
        }
    }

    public function getDailyAttendance($employee_id)
    {
        $attendance = Attendance::where('employee_id', $employee_id)->whereDate('check_in', date('Y-m-d'))->first();

        return $attendance;
    }
}