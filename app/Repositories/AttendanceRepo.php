<?php 
namespace App\Repositories;

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
}