<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Repositories\CompanyRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class CompanyController extends Controller
{
    protected $companyRepo;
    public function __construct(CompanyRepo $companyRepo)
    {
        $this->companyRepo = $companyRepo;
    }

    public function index()
    {
        return view('master.company.index');
    }

    public function dataJson()
    {
        $companies = $this->companyRepo->getAll();

        $data = DataTables::of($companies)
                ->addIndexColumn()
                ->toJson(true);

        return $data;
    }

    public function updateOrCreate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'companyId'         => 'nullable',
            'namaPerusahaan'    => 'required',
            'nomorTelpon'       => 'required',
            'alamat'            => 'required'
        ]);

        if($validator->fails()){
            $validation = $this->validatorMessage($validator);

            return $this->bad($validation['data'], 400);
        }

        try {
            $this->companyRepo->updateOrCreate($request);
        } catch (\Exception $e) {
            return $this->bad('terjadi kesalahan', 500, $e->getMessage());
        }

        return $this->ok('data berhasil disimpan', 200);
    }

    public function detail($id)
    {
        return $this->ok('company', 200, $this->companyRepo->findById($id));
    }

    public function delete($id)
    {
        try {
            $this->companyRepo->delete($id);
        } catch (\Exception $e) {
            return $this->bad('terjadi kesalahan', 500, $e->getMessage());
        }

        return $this->ok('data berhasil dihapus', 200);
    }
}
