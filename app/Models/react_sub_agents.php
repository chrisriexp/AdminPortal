<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class react_sub_agents extends Model
{
    use HasApiTokens, HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'rocket_id',
        'name',
        'rocketPlus',
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
