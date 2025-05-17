<?php

namespace App\Http\Controllers;
use App\Models\Assignment;
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

        $assignments = Assignment::whereHas('klass.students', function($query) use ($student){
            $query->where('user_id', $student->id);
        })
        >with(['class:id,name', 'teacher:id,name']) // Only select needed fields
        ->latest()
        ->get();

        return response()->json([
            'assignments' => $assignments
        ]);


    }
}
