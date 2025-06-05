<?php

namespace App\Models;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\Klass;

use Illuminate\Database\Eloquent\Model;

class SubAttendance extends Model
{
    protected $fillable =[
        'student_id',
        'klass_id',
        'subject_id',
        'date',
        'status',

    ];
    protected $dates = ['date'];

    public function student(){
        return $this->belongsTo(Student::class);
    }

    public function teacher(){
        return $this->belongsTo(Teacher::class);
    }

    public function subject(){
        return $this->belongsTo(Subject::class);
    }

    public function klass(){
        return $this->belongsTo(Klass::class);
    }

}
