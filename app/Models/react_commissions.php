<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class react_commissions extends Model
{
    use HasApiTokens, HasFactory;

    protected $primaryKey = 'statement_id';
    public $incrementing = false;

    protected $fillable = [
        'statement_id',
        'carrier',
        'month',
        'override',
        'prem',
        'comm',
        'policies',
        'uploaded_by'
    ];
}
