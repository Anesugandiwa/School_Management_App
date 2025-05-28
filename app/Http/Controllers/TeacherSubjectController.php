<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\Subject;

class TeacherSubjectController extends Controller
{
    public function index(){
        $teacher = auth()->user();
        $subjects = $teacher->subjects;

        return inertia('Teacher/subjects', [
            'subjects' => $subjects
        ]);
    }
}
