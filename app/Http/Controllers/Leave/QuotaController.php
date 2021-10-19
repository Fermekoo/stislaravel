<?php

namespace App\Http\Controllers\Leave;

use App\Http\Controllers\Controller;
use App\Repositories\LeaveRepo;
use Illuminate\Http\Request;

class QuotaController extends Controller
{
    protected $leaveRepo;
    public function __construct(LeaveRepo $leaveRepo)
    {
        $this->leaveRepo = $leaveRepo;
    }

    public function index()
    {
        return view('leave-quota.index');
    }

    public function getQuota($employee_id)
    {
        return $this->ok('quota', 200, $this->leaveRepo->getQuota($employee_id));
    }

    public function setQuota($employee_id, Request $request)
    {
        try {
            $this->leaveRepo->setQuota($employee_id, $request);
        } catch (\Exception $e) {
            return $this->bad('terjadi kesalahan', 500, $e->getMessage());
        }

        return $this->ok('data berhasil disimpan', 200);
    }
}
