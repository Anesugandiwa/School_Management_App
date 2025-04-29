<?php

namespace App\Models;
use App\Models\Teacher;
use App\Models\Klass;

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
}
