<?php

namespace App\Models;

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
        'klass',
    ];
}
