<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Repositories\DivisionRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class DivisionController extends Controller
{
    protected $divisonRepo;
    public function __construct(DivisionRepo $divisonRepo)
    {
        $this->divisonRepo = $divisonRepo;
    }

    public function index()
    {
        return view('master.division.index');
    }

    public function dataJson()
    {
        $companies = $this->divisonRepo->getAll();

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
            'divisionId' => 'nullable',
            'companyId'  => 'nullable',
            'namaDivisi' => 'required',
        ]);

        if($validator->fails()){
            $validation = $this->validatorMessage($validator);

            return $this->bad($validation['data'], 400);
        }

        try {
            $this->divisonRepo->updateOrCreate($request);
        } catch (\Exception $e) {
            return $this->bad('terjadi kesalahan', 500, $e->getMessage());
        }

        return $this->ok('data berhasil disimpan', 200);
    }

    public function detail($id)
    {
        return $this->ok('company', 200, $this->divisonRepo->findById($id));
    }

    public function delete($id)
    {
        try {
            $this->divisonRepo->delete($id);
        } catch (\Exception $e) {
            return $this->bad('terjadi kesalahan', 500, $e->getMessage());
        }

        return $this->ok('data berhasil dihapus', 200);
    }
}
