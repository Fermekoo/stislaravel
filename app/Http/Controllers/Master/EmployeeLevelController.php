<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Repositories\EmployeeLevelRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;

class EmployeeLevelController extends Controller
{
    protected $employeeLevelRepo;
    public function __construct(EmployeeLevelRepo $employeeLevelRepo)
    {
        $this->employeeLevelRepo = $employeeLevelRepo;
    }

    public function index()
    {
        return view('master.employee-level.index');
    }

    public function dataJson()
    {
        $companies = $this->employeeLevelRepo->getAll();

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
            'employeeLevelId'  => 'nullable',
            'companyId'  => Rule::requiredIf(function(){
                return auth()->user()->user_type == 'admin';
            }),
            'golongan'         => 'required',
        ]);

        if($validator->fails()){
            $validation = $this->validatorMessage($validator);

            return $this->bad($validation['data'], 400);
        }

        try {
            $this->employeeLevelRepo->updateOrCreate($request);
        } catch (\Exception $e) {
            return $this->bad('terjadi kesalahan', 500, $e->getMessage());
        }

        return $this->ok('data berhasil disimpan', 200);
    }

    public function detail($id)
    {
        return $this->ok('company', 200, $this->employeeLevelRepo->findById($id));
    }

    public function delete($id)
    {
        try {
            $this->employeeLevelRepo->delete($id);
        } catch (\Exception $e) {
            return $this->bad('terjadi kesalahan', 500, $e->getMessage());
        }

        return $this->ok('data berhasil dihapus', 200);
    }
}
