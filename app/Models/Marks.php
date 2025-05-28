<?php

namespace App\Models;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Klass;
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
        
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
    
    public function klass()
    {
        return $this->belongsTo(Klass::class);
    }
}
