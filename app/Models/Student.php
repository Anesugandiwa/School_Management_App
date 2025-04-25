<?php

namespace App\Models;
use App\Models\Klass;

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
        
    ];

    public function klass(){
        return $this->belongsTo(Klass::class);
    }
}
