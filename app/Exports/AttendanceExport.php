<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AttendanceExport implements FromView
{   
    use Exportable;
    protected $attendances;
    public function __construct($attendances)
    {
        $this->attendances = $attendances;
    }
    public function view(): View
    {
        return view('export.attendance', [
            'attendances' => $this->attendances
        ]);
    }
}
