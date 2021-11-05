<?php 
namespace App\Repositories;

use App\Models\APIKey;
use App\Support\CodeGenerator;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

class APIKeyRepo
{
    public function getAll()
    {
        return APIKey::auth()->get();
    }

    public function findBy($field, $value, $first = true)
    {
        $apikey = APIKey::with('company','company.admin')->where($field, $value);

        return ($first) ? $apikey->first() : $apikey->get();
    }

    public function create($company_id, $payloads)
    {
        $key = CodeGenerator::generateKey();
        try {
            APIKey::create([
                'api_key'       => $key,
                'company_id'    => $company_id,
                'is_active'     => true,
                'is_strict_ip'  => $payloads->whitelistIP ?? false,
                'whitelist_ip'  => ($payloads->IPwhitelist && $payloads->whitelistIP) ? $payloads->IPwhitelist : null,
                'created_by'    => auth()->user()->id
            ]);
        } catch (QueryException $e) {
            Log::warning($e->getMessage());
            throw $e;
        }

        return $key;
    }

    public function update($id, $payloads)
    {
        $key = APIKey::auth()->findOrFail($id);
        try {
            $key->is_strict_ip  = $payloads->whitelistIP ?? false;
            $key->whitelist_ip  = ($payloads->IPwhitelist && $payloads->whitelistIP) ? $payloads->IPwhitelist : null;
            $key->save();
        } catch (QueryException $e) {
            Log::warning($e->getMessage());
            throw $e;
        }
    }

    public function delete($id)
    {
        return APIKey::auth()->where('id',$id)->delete();
    }

    public function changeStatus($id)
    {
        $key = APIKey::auth()->findOrFail($id);

        try {
            $key->is_active = ($key->is_active) ? false : true;
            $key->save();
        } catch (QueryException $e) {
            Log::warning($e->getMessage());
            throw $e;
        }
    }
}