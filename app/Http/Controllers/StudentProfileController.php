<?php

namespace App\Http\Controllers;
use App\Models\Assignment;
use App\Models\Klass;
use Illuminate\Http\Request;

class StudentProfileController extends Controller
{
    public function index(){
        $student = auth()->user();

        return inertia('Students/studentprof',[
        'student' =>  $student,
        
    ]);

    }

    public function assignment(){
        $student = auth()->user();

        $klassId = $student->klass_id;

        $assignments = Assignment::where('klass_id', $klassId)
            ->with(['teacher', 'subject','klass'])
            ->latest()
            ->get();
        return inertia('Students/studentAssignment',[
            'assignments' => $assignments
        ]);




    }
}
