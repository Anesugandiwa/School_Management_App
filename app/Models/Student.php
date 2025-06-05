<?php

namespace App\Models;
use App\Models\Klass;
use App\Models\User;
use App\Models\Marks;
use App\Models\SubAttendance;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable =[
        'name',
        'surname',
        'gender',
        'date_of_birth',
        'address',
        'contact',
        'nationality',
        'klass_id',
        'user_id',
        
    ];

    public function klass(){
        return $this->belongsTo(Klass::class);
    }

    
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function marks(){
        return $this->hasMany(Marks::class);
    }

    public function subAttendances(){
        return $this->hasMany(SubAttendance::class);
    }
}
