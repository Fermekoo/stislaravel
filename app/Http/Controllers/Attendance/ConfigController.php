<?php

namespace App\Http\Controllers\Attendance;

use App\Http\Controllers\Controller;
use App\Repositories\AttendanceRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ConfigController extends Controller
{
    protected $attendanceRepo;
    public function __construct(AttendanceRepo $attendanceRepo)
    {
        $this->attendanceRepo = $attendanceRepo;
    }

    public function index()
    {
        return view('time-config.index');
    }

    public function getTimeConfig($company_id)
    {
        $company_id = (auth()->user()->user_type == 'admin') ? $company_id : auth()->user()->company_id;

        $time_config = $this->attendanceRepo->getTimeConfig($company_id);

        return $this->ok('time config', 200, $time_config);
    }

    public function updateTimeConfig(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'perusahaan'  => Rule::requiredIf(function(){
                return auth()->user()->user_type == 'admin';
            }),
            'jamMasuk'      => 'required',
            'batasJamMasuk' => 'required|after_or_equal:jamMasuk',
            'jamPulang'     => 'required|after:batasJamMasuk',
        ]);

        if($validator->fails()){
            $validation = $this->validatorMessage($validator);

            return $this->bad($validation['data'], 400);
        }

        $company_id = (auth()->user()->user_type == 'admin') ? $request->perusahaan : auth()->user()->company_id;
        try {
            $this->attendanceRepo->updateTimeConfig($company_id, $request);
        } catch (\Exception $e) {
            return $this->bad('terjadi kesalahan', 500, $e->getMessage());
        }

        return $this->ok('data berhasil disimpan', 200);

    }
}
