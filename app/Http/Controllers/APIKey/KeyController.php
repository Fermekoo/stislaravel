<?php

namespace App\Http\Controllers\APIKey;

use App\Http\Controllers\Controller;
use App\Repositories\APIKeyRepo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class KeyController extends Controller
{
    protected $apiKey;
    public function __construct(APIKeyRepo $apiKey)
    {
        $this->apiKey = $apiKey;
    }

    public function index()
    {
        return view('apikey.index');
    }

    public function dataJson()
    {
        $keys = $this->apiKey->getAll();

        $data = DataTables::of($keys)
                ->addIndexColumn()
                ->addColumn('company', function($row){
                    return $row->company->name;
                })
                ->addColumn('creator', function($row){
                    return $row->creator->username;
                })
                ->toJson();

        return $data;
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'perusahaan'     => Rule::requiredIf(function(){
                return auth()->user()->user_type == 'admin';
            }),
            'whitelistIP'   => 'nullable|boolean',
            'IPwhitelist'   => 'required_if:whitelistIP,true|array',
            'IPwhitelist.*' => 'bail|required_if:whitelistIP,true'
        ]);

        if($validator->fails()){
            $validation = $this->validatorMessage($validator);

            return $this->bad($validation['data'], 400);
        }

        $company_id = (auth()->user()->user_type == 'admin') ? $request->perusahaan : auth()->user()->company_id;

        try {

            $this->apiKey->create($company_id, $request);

        } catch (\Exception $e) {
            return $this->bad('terjadi kesalahan', 500, $e->getMessage());
        }

        return $this->ok('data berhasil disimpan', 200);
    }

    public function delete($id)
    {
        try {

            $this->apiKey->delete($id);

        } catch (\Exception $e) {
            return $this->bad('terjadi kesalahan', 500, $e->getMessage());
        }

        return $this->ok('data berhasil dihapus', 200);
    }

    public function changeStatus($id)
    {
        try {

            $this->apiKey->changeStatus($id);

        } catch (\Exception $e) {
            return $this->bad('terjadi kesalahan', 500, $e->getMessage());
        }

        return $this->ok('data berhasil diubah', 200);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'            => 'required',
            'whitelistIP'   => 'nullable|boolean',
            'IPwhitelist'   => 'required_if:whitelistIP,true|array',
            'IPwhitelist.*' => 'bail|required_if:whitelistIP,true'
        ]);

        if($validator->fails()){
            $validation = $this->validatorMessage($validator);

            return $this->bad($validation['data'], 400);
        }

        try {

            $this->apiKey->update($request->id, $request);

        } catch (\Exception $e) {
            return $this->bad('terjadi kesalahan', 500, $e->getMessage());
        }

        return $this->ok('data berhasil disimpan', 200);


    }
}
