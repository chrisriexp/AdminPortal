<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class quickTasks extends Model
{
    use HasApiTokens, HasFactory;

    protected $fillable = [
        'user',
        'task',
        'date',
        'important'
    ];
}
