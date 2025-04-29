<?php

namespace App\Http\Controllers;
use App\Models\Klass;
use App\Models\Subject;
use App\Models\Teacher;

use Illuminate\Http\Request;

class MarksController extends Controller
{
    public function index(){
        // $teacher = Teacher::where('user_id', auth()->id())->with('subjects')->first();
        $teacher = auth()->user();

        return inertia('Teacher/addmarks', [
            'klasses' => Klass::all(),
            // 'subjects' => $teacher->subjects,
        ]);
    }

    // public function fetchStudents(Request $request){
    //     request->validate([
    //         'klass_id' => 'required|exists:klasses,id',
    //         'subject_id' => 'required|exists:subjects,id',

    //     ]);
    //     $students = Student::where('klass_id', $request->klass_id)
    //         ->whereHas('subjects', function($query) use ($request) {
    //             $query->where('subject_id', $request->subject_id);
    //         })
    //         ->get();

    //         return response()->json($students);

    // }

    public function fetchStudents(Request $request)
{
    $request->validate([
        'klass_id' => 'required|exists:klasses,id',
        
    ]);

    $students = Student::where('klass_id', $request->klass_id)->get();

    return response()->json($students);
}
}
