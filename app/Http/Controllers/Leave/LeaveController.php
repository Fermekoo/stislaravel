<?php

namespace App\Http\Controllers\Leave;

use App\Http\Controllers\Controller;
use App\Repositories\LeaveRequestRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class LeaveController extends Controller
{
    protected $lrRepo;
    public function __construct(LeaveRequestRepo $lrRepo)
    {
        $this->lrRepo = $lrRepo;
    }

    public function index()
    {
        return view('leave.index');
    }

    public function dataJson()
    {
        $leaves = $this->lrRepo->getAll();

        $data = DataTables::of($leaves)
                ->addIndexColumn()
                ->toJson();
        return $data;
    }

    public function updateOrCreate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'izinId'          => 'nullable',
            'jenisIzin'       => 'required',
            'tanggalMulai'    => 'required|date_format:Y-m-d',
            'tanggalSelesai'  => 'required|after:tanggalMulai',
            'keterangan'      => 'nullable',
            'document'        => 'nullable|mimes:jpg,png,jpeg,pdf,docx,doc'
        ]);

        if($validator->fails()){
            $validation = $this->validatorMessage($validator);

            return $this->bad($validation['data'], 400);
        }

        $employee_id = auth()->user()->employee->id;

        try {

            ($request->izinId) ? $this->lrRepo->update($request->izinId, $request) : $this->lrRepo->create($employee_id, $request);

        } catch (\Exception $e) {

            return $this->bad('terjadi kesalahan', 500, $e->getMessage());
        }

        return $this->ok('data berhasil disimpan', 200);
    }

    public function findById($id)
    {
        return $this->ok('detail', 200, $this->lrRepo->findById($id));
    }

    public function delete($id)
    {
        try {

            $this->lrRepo->delete($id);

        } catch (\Exception $e) {

            return $this->bad('terjadi kesalahan', 500, $e->getMessage());
        }

        return $this->ok('data berhasil dihapus', 200);
    }
}
