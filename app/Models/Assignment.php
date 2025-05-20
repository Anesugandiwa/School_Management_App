<?php

namespace App\Models;
use App\Models\Klass;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $fillable = [
        'klass_id',
        'subject_id',
        'title',
        'description',
        'due_date'
    ];

    public function klass(){
        return $this->belongsTo(Klass::class);
    }

    public function subject(){
        
    return $this->belongsTo(Subject::class);
    }

    public function techer(){
         return $this->belongsTo(Teacher::class);
    }
}
