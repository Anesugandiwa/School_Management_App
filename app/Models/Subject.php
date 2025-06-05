<?php

namespace App\Models;
use App\Models\Teacher;
use App\Models\Klass;
use App\Models\TimeTable;
use App\Models\SubAttendance;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'name',
        // 'teacher_id',
        'teacher',
        'is_optional',
        'department'
    ];

    public function teachers(){
        return $this->belongsToMany(Teacher::class, 'subject_teacher');
    }

    public function klasses(){
        return $this->belongsToMany(Klass::class);
    }
    public function teacherKlasses(){
        return $this->belongsToMany(Teacher::class, 'klass_subject_teacher')
            ->withPivot('klass_id')
            ->withTimestamps();
    }
    
    public function timetables(){
        return $this->hasMany(TimeTable::class);
    }
    public function subAttendances(){
        return $this->hasMany(SubAttendance::class);
    }
}
