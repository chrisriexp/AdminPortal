<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class react_commission_policies extends Model
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'month',
        'carrier',
        'carrier_id',
        'policy',
        'insured',
        'eff',
        'trans_type',
        'policy_type',
        'prem',
        'comm',
        'override'
    ];
}
