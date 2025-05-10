<?php

namespace App\Http\Controllers;
use App\Models\Klass;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Marks;

use Illuminate\Http\Request;

class MarksController extends Controller
{

    public function index(Request $request)
{
    $teacher = auth()->user()->teacher;
    $klasses = $teacher->assignedKlasses;
    $subjects = $teacher->assignedSubjects;

    return inertia('Teacher/addmarks', [
        'klasses' => $klasses,
        'subjects' => $subjects,
    ]); 
}

public function fetchStudents(Request $request)
{
    $request->validate([
        'klass_id' => 'required|exists:klasses,id',
    ]);

    $klass = Klass::find($request->klass_id);
    
    if ($klass) {
        return response()->json($klass->students);
    }
    
    return response()->json([]);
}
public function store(Request $request){
    $data = $request->validate([
        'klass_id' => 'required|exists:klasses,id',
        'subject_id' => 'required|exists:subjects,id',
        'marks' => 'required|array',
        'marks.*.student_id' => 'required|exists:students,id',
        'marks.*.mark' => 'required|numeric|min:0|max:100',
    ]);
    foreach ($data['marks'] as $markData){
        Marks::updateOrCreate([
            'student_id' => $markData['student_id'],
            'subject_id' => $data['subject_id'],
            'klass_id' => $data['klass_id'],
            'year' => now()->year,

        ],
        [
            'marks_obtained' => $markData['mark'],
            'total_marks' => 100,
        ]
    );
    }
}


}
