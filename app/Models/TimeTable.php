<?php

namespace App\Models;
use App\Models\Klass;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Model;

class TimeTable extends Model
{
        protected $fillable = [
        'klass_id',
        'subject_id',
        'teacher_id',
        'day_of_week',
        'start_time',
        'end_time',
        
    ];

    public function klass(){
        return $this->belongsTo(Klass::class);
    }

    public function subject(){
        return $this->belongsTo(Subject::class);
    }
    public function teacher(){
        return $this->belongsTo(Teacher::class);
    }
}
