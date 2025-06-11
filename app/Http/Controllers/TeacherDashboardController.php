<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\KlassSubjectTeacher;
use App\Models\Teacher;
use Illuminate\Http\Request;

class TeacherDashboardController extends Controller
{
    public function index(){
        return inertia('Teacher/display');
    }
    public function klass(){
    $user = auth()->user();
    
    // Find the teacher record associated with this user
    $teacher = $user->teacher;
    
    if (!$teacher) {
        return inertia('Teacher/klass', [
            'klasses' => [],
            'teacher' => null,
            'totalClasses' => 0,
            'totalSubjects' => 0,
            'error_message' => 'No teacher profile found for this user. Please contact admin.'
        ]);
    }
    
    // Now use the actual teacher ID (from teachers table)
    $teacherAssignments = KlassSubjectTeacher::with(['klass', 'subject'])
        ->where('teacher_id', $teacher->id) // This is teachers.id, not users.id
        ->get();
    
    if ($teacherAssignments->isEmpty()) {
        return inertia('Teacher/klass', [
            'klasses' => [],
            'teacher' => $teacher,
            'totalClasses' => 0,
            'totalSubjects' => 0,
            'debug_info' => [
                'user_id' => $user->id,
                'teacher_id' => $teacher->id,
                'message' => 'No assignments found for teacher ID: ' . $teacher->id
            ]
        ]);
    }
    
    $klassesWithSubjects = $teacherAssignments->groupBy('klass_id')->map(function ($assignments, $klassId) {
        $klass = $assignments->first()->klass;
        $subjects = $assignments->pluck('subject');
        
        return [
            'id' => $klass->id,
            'class_name' => $klass->class_name,
            'year' => $klass->year,
            'department' => $klass->department,
            'subjects' => $subjects->toArray(),
            'subject_count' => $subjects->count(),
        ];
    })->values();
    
    return inertia('Teacher/klass', [
        'klasses' => $klassesWithSubjects,
        'teacher' => $teacher,
        'user' => $user,
        'totalClasses' => $klassesWithSubjects->count(),
        'totalSubjects' => $teacherAssignments->count(),
    ]);
}
   

    
}
