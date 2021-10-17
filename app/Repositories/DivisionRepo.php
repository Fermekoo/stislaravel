<?php 
namespace App\Repositories;

use App\Models\Division;
use App\Support\CodeGenerator;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class DivisionRepo
{
    public function getAll()
    {
        return Division::auth()->get();
    }

    public function updateOrCreate($request)
    {
        $division = Division::auth()->find($request->divisionId);

        try {

            if($division) {
                $division->company_id = (auth()->user()->user_type == 'admin') ? $request->companyId : $division->company_id;
                $division->name       = $request->namaDivisi;
                $division->save();

            } else {
                Division::create([
                    'company_id'    => (auth()->user()->user_type == 'admin') ? $request->companyId : auth()->user()->company_id,
                    'division_code' => CodeGenerator::divisionCode(),
                    'name'          => $request->namaDivisi,
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
        return Division::auth()->findOrFail($id);
    }

    public function delete($id)
    {
        return Division::auth()->where('id',$id)->delete();
    }
}