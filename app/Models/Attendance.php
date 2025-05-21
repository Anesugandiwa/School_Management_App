<?php

namespace App\Models;
use App\Models\Student;
use App\Models\Klass;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'student_id', 
        'class_id', 
        'date', 
        'status'
    ];
        protected $casts = [
        'date' => 'date'
    ];
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    
    public function klass() // Using klass instead of class to avoid PHP keyword
    {
        return $this->belongsTo(Klass::class, 'class_id');
    }
}
