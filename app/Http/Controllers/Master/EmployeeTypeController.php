<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Repositories\EmployeeTypeRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class EmployeeTypeController extends Controller
{
    protected $employeeTypeRepo;
    public function __construct(EmployeeTypeRepo $employeeTypeRepo)
    {
        $this->employeeTypeRepo = $employeeTypeRepo;
    }

    public function index()
    {
        return view('master.employee-type.index');
    }

    public function dataJson()
    {
        $companies = $this->employeeTypeRepo->getAll();

        $data = DataTables::of($companies)
                ->addIndexColumn()
                ->addColumn('company', function($row){
                    return $row->company->name;
                })
                ->toJson(true);

        return $data;
    }

    public function updateOrCreate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employeeTypeId'  => 'nullable',
            'companyId'       => 'nullable',
            'tipeKaryawan'    => 'required',
        ]);

        if($validator->fails()){
            $validation = $this->validatorMessage($validator);

            return $this->bad($validation['data'], 400);
        }

        try {
            $this->employeeTypeRepo->updateOrCreate($request);
        } catch (\Exception $e) {
            return $this->bad('terjadi kesalahan', 500, $e->getMessage());
        }

        return $this->ok('data berhasil disimpan', 200);
    }

    public function detail($id)
    {
        return $this->ok('company', 200, $this->employeeTypeRepo->findById($id));
    }

    public function delete($id)
    {
        try {
            $this->employeeTypeRepo->delete($id);
        } catch (\Exception $e) {
            return $this->bad('terjadi kesalahan', 500, $e->getMessage());
        }

        return $this->ok('data berhasil dihapus', 200);
    }
}
