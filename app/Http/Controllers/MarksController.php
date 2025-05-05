<?php

namespace App\Http\Controllers;
use App\Models\Klass;
use App\Models\Subject;
use App\Models\Teacher;

use Illuminate\Http\Request;

class MarksController extends Controller
{

    public function index(Request $request)
{
    $teacher = auth()->user()->teacher;
    $subjects = $teacher ? $teacher->subjects : [];
    $klasses = $teacher?->klasses ?? [];

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



}
