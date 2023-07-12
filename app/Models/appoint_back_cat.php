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
}
