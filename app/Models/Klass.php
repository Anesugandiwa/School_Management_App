<?php

namespace App\Models;
use App\Models\Teacher;
use App\Models\Student;
use Illuminate\Database\Eloquent\Model;

class Klass extends Model
{
    protected $fillable = [
        'class_name',
        'year',
        'department',
        'teacher_id',
    ];

    public function teacher(){
        return $this->belongsTo(Teacher::class);
    }

    public function student(){
        return $this->hasMany(Student::class);
    }

}
