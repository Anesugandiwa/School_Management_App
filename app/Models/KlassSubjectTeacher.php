<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KlassSubjectTeacher extends Model
{
    protected $table = 'klass_subject_teacher';
    
    protected $fillable = [
        'klass_id',
        'subject_id',
        'teacher_id',

    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function klass()
    {
        return $this->belongsTo(Klass::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
    
}
