<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $fillable = [
        'klass_id',
        'subject_id',
        'title',
        'description',
        'due_date'
    ];
}
