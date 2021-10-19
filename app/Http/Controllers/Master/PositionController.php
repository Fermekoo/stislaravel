<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Repositories\PositionRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Validation\Rule;

class PositionController extends Controller
{
    protected $positionRepo;
    public function __construct(PositionRepo $positionRepo)
    {
        $this->positionRepo = $positionRepo;
    }

    public function index()
    {
        return view('master.position.index');
    }

    public function dataJson()
    {
        $companies = $this->positionRepo->getAll();

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
            'positionId'  => 'nullable',
            'companyId'  => Rule::requiredIf(function(){
                return auth()->user()->user_type == 'admin';
            }),
            'namaJabatan' => 'required',
        ]);

        if($validator->fails()){
            $validation = $this->validatorMessage($validator);

            return $this->bad($validation['data'], 400);
        }

        try {
            $this->positionRepo->updateOrCreate($request);
        } catch (\Exception $e) {
            return $this->bad('terjadi kesalahan', 500, $e->getMessage());
        }

        return $this->ok('data berhasil disimpan', 200);
    }

    public function detail($id)
    {
        return $this->ok('company', 200, $this->positionRepo->findById($id));
    }

    public function delete($id)
    {
        try {
            $this->positionRepo->delete($id);
        } catch (\Exception $e) {
            return $this->bad('terjadi kesalahan', 500, $e->getMessage());
        }

        return $this->ok('data berhasil dihapus', 200);
    }
}
