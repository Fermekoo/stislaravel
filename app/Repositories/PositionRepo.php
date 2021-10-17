<?php 
namespace App\Repositories;

use App\Models\Position;
use App\Support\CodeGenerator;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class PositionRepo
{
    public function getAll()
    {
        return Position::auth()->get();
    }

    public function updateOrCreate($request)
    {
        $position = Position::auth()->find($request->positionId);

        try {

            if($position) {
                $position->company_id = (auth()->user()->user_type == 'admin') ? $request->companyId : $position->company_id;
                $position->name       = $request->namaJabatan;
                $position->save();

            } else {
                Position::create([
                    'company_id'    => (auth()->user()->user_type == 'admin') ? $request->companyId : auth()->user()->company_id,
                    'role_code'     => CodeGenerator::positionCode(),
                    'name'          => $request->namaJabatan,
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
        return Position::auth()->findOrFail($id);
    }

    public function delete($id)
    {
        return Position::auth()->where('id',$id)->delete();
    }
}