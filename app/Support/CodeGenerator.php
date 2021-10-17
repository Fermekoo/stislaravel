<?php 
namespace App\Support;

use App\Models\Company;
use App\Models\Division;
use App\Models\Position;

class CodeGenerator
{
    public static function companyCode()
    {
        $last_id = Company::orderBy('id','desc')->first();
        $new_id  = ($last_id) ? $last_id->id + 1 : 1;

        $code = 'PRN-'.str_pad($new_id, 6, 0, STR_PAD_LEFT);

        return $code;
    }

    public static function divisionCode()
    {
        $last_id = Division::orderBy('id','desc')->first();
        $new_id  = ($last_id) ? $last_id->id + 1 : 1;

        $code = 'DIV-'.str_pad($new_id, 6, 0, STR_PAD_LEFT);

        return $code;
    }
    public static function positionCode()
    {
        $last_id = Position::orderBy('id','desc')->first();
        $new_id  = ($last_id) ? $last_id->id + 1 : 1;

        $code = 'JBT-'.str_pad($new_id, 6, 0, STR_PAD_LEFT);

        return $code;
    }
}