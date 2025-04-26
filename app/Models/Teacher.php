<?php

namespace App\Models;
use Str;
use Illuminate\Support\Facades\Hash;
use App\Models\Klass;
use App\Models\Subject;
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

    // protected static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($teacher) {
    //         // Generate password from first name + random 4 digits
    //         $basePassword = Str::slug($teacher->first_name); // Clean the name
            
    //         $teacher->password = Hash::make($basePassword);
            
    //         // If you want to store the plaintext temporarily (not recommended)
    //         // $teacher->plain_password = $basePassword . $randomDigits;
    //     });
    // }

    public function klass(){
        return $this->hasMany(Klass::class);
    }

    public function subjects(){
        return $this->belongsToMany(Subject::class, 'subject_teacher', 'teacher_id', 'subject_id');
    }

    public function user()
{
    return $this->belongsTo(User::class);
}
}
