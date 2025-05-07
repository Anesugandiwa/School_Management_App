<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index(Request $request)
    {
        $teacher = auth()->user()->teacher;
        $subjects = $teacher ? $teacher->subjects : [];
        $klasses = $teacher?->klasses ?? [];
    
        return inertia('Teacher/assignment', [
            'klasses' => $klasses,
            'subjects' => $subjects,
        ]); 
    }
}
