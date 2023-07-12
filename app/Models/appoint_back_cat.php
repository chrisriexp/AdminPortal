<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class appoint_back_cat extends Model
{
    use HasApiTokens, HasFactory;

    protected $primaryKey = 'rocket_id';
    public $incrementing = false;

    protected $fillable = [
        'status',
        'document',
        'data',
        'appointed'
    ];

    protected $attributes = [
        'data' => '{
            "agency_name": null,
            "agent_name": null,
            "phone": null,
            "email": null,
            "address1": null,
            "address2": null,
            "city": null,
            "state": null,
            "zip": null,
            "agency_type": null,
            "agency_tax_id": null,
            "agency_license": null,
            "agent_npn": null,
            "eo_policy": null,
            "eo_insurer": null,
            "eo_limit": null,
            "eo_exp": null
        }'
    ];
}
