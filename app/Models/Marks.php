<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Marks extends Model
{
    protected $fillable =[
        'student_id',
        'subject_id',
        'klass_id',
        'exam_type',
        'term',
        'year',
        'marks',
        'total_marks',
        'grade',
        'remarks',
    ];
}
