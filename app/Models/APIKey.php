<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class APIKey extends Model
{
    protected $table    = 'api_keys';
    protected $guarded  = [];
    protected $casts    = [
        'whitelist_ip' => 'array'
    ];

    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeAuth($query)
    {
        if (auth()->check()) {
            return $query->when(auth()->user()->user_type != 'admin', function ($q) {
                return $q->where('company_id', auth()->user()->company_id);
            });
        }
    }
}
