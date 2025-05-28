<?php

namespace App\Models;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Model;

class Klass extends Model
{
    protected $fillable = [
        'class_name',
        'year',
        'department',
        'teacher_id',
        'subject_id',
    ];

    public function teacher(){
        return $this->belongsTo(Teacher::class);
    }

    public function students(){
        return $this->hasMany(Student::class);
    }
    public function subjects(){
        return $this->belongsToMany(Subject::class);

    }
    public function subjectTeachers()
    {
        return $this->belongsToMany(Teacher::class, 'klass_subject_teacher')
        ->withPivot('subject_id')
        ->withTimestamps();
    }

}
