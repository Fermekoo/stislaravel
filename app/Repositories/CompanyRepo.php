<?php 
namespace App\Repositories;

use App\Models\Company;
use App\Models\User;
use App\Support\CodeGenerator;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Exceptions\RoleDoesNotExist;

class CompanyRepo
{
    public function getAll()
    {
        return Company::get();
    }

    public function updateOrCreate($request)
    {
        DB::beginTransaction();
        $company = Company::find($request->companyId);

        try {

            if($company) {
                $company->name    = $request->namaPerusahaan;
                $company->address = $request->alamat;
                $company->phone   = $request->nomorTelpon;
                $company->save();

                $user = User::where('user_type', 'company')->where('company_id', $company->id)->first();

                $user->username  = $request->username;
                $user->password  = ($request->password) ? Hash::make($request->password) : $user->password;
                $user->save();

            } else {
               $company = Company::create([
                    'company_code' => CodeGenerator::companyCode(),
                    'name'         => $request->namaPerusahaan,
                    'address'      => $request->alamat,
                    'phone'        => $request->nomorTelpon
                ]);

               $user = User::create([
                    'username'   => $request->username,
                    'password'   => Hash::make($request->password),
                    'user_type'  => 'company',
                    'company_id' => $company->id
                ]);

                $user->assignRole('ROOT');
            }
            
        } catch (QueryException $e) {
            DB::rollBack();
            Log::warning($e->getMessage());
            throw $e;

        } catch (RoleDoesNotExist $e) {
            DB::rollBack();
            Log::warning($e->getMessage());
            throw $e;
        }
        
        DB::commit();
        return true;
    }

    public function findById($id)
    {
        return Company::with('admin:id,company_id,username')->findOrFail($id);
    }

    public function delete($id)
    {
        return Company::destroy($id);
    }

    public function companyUserId($company_id)
    {
        $user = User::where('user_type','company')->where('company_id', $company_id)->first();

        return ($user) ? $user->id : null;
    }
}