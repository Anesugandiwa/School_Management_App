<?php

namespace App\Models;
use Str;
use Illuminate\Support\Facades\Hash;
use App\Models\Klass;
use App\Models\Subject;
use App\Models\TimeTable;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = [
       
        'first_name',
        'last_name',
        'gender',
        'Date_Of_Birth',
        'national_id',
        'email',
        'phone',
        'address',
        'department',
        'password',
        'user_id',
        'subject_id',
        'klass_id'
    ];



    // public function klasses(){
    //     return $this->hasMany(Klass::class);
    // }

    public function subjects(){
        return $this->belongsToMany(Subject::class, 'subject_teacher', 'teacher_id', 'subject_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function klassSubjects()
    {
        return $this->belongsToMany(Klass::class, 'klass_subject_teacher')
        ->withPivot('subject_id')
        ->withTimestamps();
    }
    public function assignedKlasses()
    {
        return $this->belongsToMany(Klass::class, 'klass_subject_teacher', 'teacher_id', 'klass_id')->distinct();
    }

    public function assignedSubjects()
    {
        return $this->belongsToMany(Subject::class, 'klass_subject_teacher', 'teacher_id', 'subject_id')->distinct();
    }

    public function timetables(){
        return $this->hasMany(TimeTable::class);
    }
}
