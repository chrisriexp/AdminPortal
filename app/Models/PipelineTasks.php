<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class PipelineTasks extends Model
{
    use HasApiTokens, HasFactory;

    protected $primaryKey = 'id';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'project_id',
        'section_id',
        'name',
        'assigned',
        'due_date',
        'priority',
        'progress',
        'tags',
        'desc',
        'created_by',
        'completed'
    ];
}
