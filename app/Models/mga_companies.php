<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class mga_companies extends Model
{
    use HasApiTokens, HasFactory;

    protected $primaryKey = 'rocket_id';
    public $incrementing = false;

    protected $fillable = [
        'rocket_id',
        'name',
        'aon',
        'beyond',
        'cat',
        'dual',
        'flow',
        'neptune',
        'palomar',
        'sterling',
        'wright'
    ];
}
