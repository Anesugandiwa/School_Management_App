<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\KlassSubjectTeacher;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Klass;
use App\Models\Attendance;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Carbon\Carbon;


class TeacherDashboardController extends Controller
{
    public function index(){
        $teacher = auth()->user()->teacher;
        $count = Assignment::whereHas('klass.subjectTeachers', function ($query) use ($teacher) {
                $query->where('teacher_id', $teacher->id);
        })
        ->where('due_date', '>=', \Carbon\Carbon::today())
        ->count();
        
        $assignments = Assignment::whereHas('klass.subjectTeachers', function ($query) use ($teacher) {
            $query->where('teacher_id', $teacher->id);
        })
            ->where('due_date', '>=', now())
            ->with(['klass', 'subject']) // eager load relationships
            ->orderBy('due_date', 'asc')
            ->get();

        return inertia('Teacher/display',[
            'assignments' => $assignments,
            'stats' => [
                'upcomingAssignments' => $count,
        ]
        ]);
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


public function getAttendanceRate()
{
    $teacher = auth()->user()->teacher;

    // Get class assigned to the teacher
    $klass = Klass::where('teacher_id', $teacher->id)->first();

    if (!$klass) {
        return response()->json(['attendance' => 0]); // No class assigned
    }

    // Count all students in the class
    $studentsCount = Student::where('klass_id', $klass->id)->count();

    // Count total attendance entries for students in that class
    $attendancesCount = Attendance::whereHas('student', function ($query) use ($klass) {
        $query->where('klass_id', $klass->id);
    })->count();

    // Get total days attendance was marked (assuming one record per student per day)
    $distinctDates = Attendance::whereHas('student', function ($query) use ($klass) {
        $query->where('klass_id', $klass->id);
    })->distinct('date')->count('date');

    // Total possible attendance records = students × distinct dates
    $totalPossible = $studentsCount * $distinctDates;

    // Calculate percentage
    $attendanceRate = $totalPossible > 0
        ? round(($attendancesCount / $totalPossible) * 100, 2)
        : 0;

    return response()->json(['attendance' => $attendanceRate]);
}

public function studentCount (){
    $teacher = auth()->user()->teacher;
    $klass = Klass::where('teacher_id', $teacher->id)->first();

    $studentCount = Student::where('klass_id', $klass->id)->count();

    return response()->json(['studentCount' =>$studentCount]);
}




   

    
}
