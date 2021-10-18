<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Repositories\EmployeeRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class EmployeeController extends Controller
{
    protected $employeRepo;
    public function __construct(EmployeeRepo $employeRepo)
    {
        $this->employeRepo = $employeRepo;
    }

    public function index()
    {
        return view('employee.index');
    }

    public function dataJson()
    {
        $employees = $this->employeRepo->getAll();
        $data = DataTables::of($employees)
                ->addColumn('company', function($row){
                    return ($row->company) ? $row->company->name : '';
                })
                ->addColumn('division', function($row){
                    return ($row->division) ? $row->division->name : '';
                })
                ->addColumn('position', function($row){
                    return ($row->position) ? $row->position->name : '';
                })
                ->addColumn('level', function($row){
                    return ($row->level) ? $row->level->name : '';
                })
                ->addColumn('type', function($row){
                    return ($row->type) ? $row->type->name : '';
                })
                ->toJson();

        return $data;
    }

    public function updateOrCreate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'employeeId'   => 'nullable',
            'perusahaan'   => 'nullable',
            'fotoKaryawan' => 'nullable|image|mimes:jpg,png,jpeg',
            'divisi'       => 'required',
            'jabatan'      => 'required',
            'golongan'     => 'required',
            'status'       => 'required',
            'namaLengkap'  => 'required',
            'jenisKelamin' => 'required|in:Laki-Laki,Perempuan',
            'nomorHp'      => 'required|numeric',
            'alamat'       => 'required',
            'username'     => 'required|unique:users,username,'.$this->employeRepo->getUserId($request->employeeId),
            'password'     => 'nullable|min:6'
        ]);

        if($validator->fails()){
            $validation = $this->validatorMessage($validator);

            return $this->bad($validation['data'], 400);
        }

        try {

            ($request->employeeId) ? $this->employeRepo->update($request->employeeId, $request) : $this->employeRepo->create($request);

        } catch (\Exception $e) {
            return $this->bad('terjadi kesalahan', 500, $e->getMessage());
        }

        return $this->ok('data berhasil disimpan', 200);

    }

    public function detail($id)
    {
        return $this->ok('detail', 200, $this->employeRepo->findById($id));
    }

    public function delete($id)
    {
        try {

            $this->employeRepo->delete($id);

        } catch (\Exception $e) {
            return $this->bad('terjadi kesalahan', 500, $e->getMessage());
        }

        return $this->ok('data berhasil dihapus', 200);
    }
}
