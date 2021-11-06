<?php 
namespace App\Repositories;

use App\Models\LeaveRequest;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class LeaveRequestRepo
{
    public function getAll()
    {
        return LeaveRequest::auth()->get();
    }

    public function findById($id)
    {
        return LeaveRequest::auth()->find($id);
    }

    public function create($employee_id, $payloads)
    {
        $document = null;
        if($payloads->hasFile('document')) :
            $document = Str::random(20).'.'.$payloads->document->getClientOriginalExtension();
            $payloads->file('document')->storeAs('document-izin', $document, 'public');
        endif;

        try {
           return LeaveRequest::create([
                'employee_id'   => $employee_id,
                'request_type'  => $payloads->jenisIzin,
                'description'   => $payloads->keterangan,
                'document'      => $document,
                'start_date'    => $payloads->tanggalMulai,
                'end_date'      => $payloads->tanggalSelesai,
                'updated_by'    => auth()->user()->id,      
            ]);
        } catch (QueryException $e) {
            Log::warning($e->getMessage());
            throw $e;
        }
    }

    public function update($id, $payloads)
    {
        $leave = LeaveRequest::auth()->where('status','Pending')->findOrFail($id);

        $document = $leave->document;
        if($payloads->hasFile('document')) :
            $document = Str::random(20).'.'.$payloads->document->getClientOriginalExtension();
            $payloads->file('document')->storeAs('document-izin', $document, 'public');
        endif;

        try {
            $leave->request_type = $payloads->jenisIzin;
            $leave->description  = $payloads->keterangan;
            $leave->document     = $document;
            $leave->start_date   = $payloads->tanggalMulai;
            $leave->end_date     = $payloads->tanggalSelesai;
            $leave->updated_by   = auth()->user()->id;
            $leave->save();
        } catch (QueryException $e) {
            Log::warning($e->getMessage());
            throw $e;
        }
    }

    public function approval($id, $status)
    {
        $leave = LeaveRequest::auth()->findOrFail($id);

        try {
            $leave->status = $status;
            $leave->save();
        } catch (QueryException $e) {
            Log::warning($e->getMessage());
            throw $e;
        }
    }

    public function delete($id)
    {
        return LeaveRequest::auth()->where('status', 'Pending')->where('id', $id)->delete();
    }
}