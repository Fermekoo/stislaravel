<?php 
namespace App\Repositories;

use App\Models\Company;
use App\Support\CodeGenerator;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class CompanyRepo
{
    public function getAll()
    {
        return Company::get();
    }

    public function updateOrCreate($request)
    {
        $company = Company::find($request->companyId);

        try {

            if($company) {
                $company->name    = $request->namaPerusahaan;
                $company->address = $request->alamat;
                $company->phone   = $request->nomorTelpon;
                $company->save();

            } else {
                Company::create([
                    'company_code' => CodeGenerator::companyCode(),
                    'name'         => $request->namaPerusahaan,
                    'address'      => $request->alamat,
                    'phone'        => $request->nomorTelpon
                ]);
            }
            
        } catch (QueryException $e) {
            Log::warning($e->getMessage());
            throw $e;
        }
        
        return true;
    }

    public function findById($id)
    {
        return Company::findOrFail($id);
    }

    public function delete($id)
    {
        return Company::destroy($id);
    }
}