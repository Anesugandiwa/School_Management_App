<?php

namespace App\Http\Controllers;
use App\Models\Klass;
use App\Models\Subject;
use App\Models\SubAttendance;
use Illuminate\Http\Request;

class SubAttendanceController extends Controller
{
    public function index(){
        $teacher = auth()->user()->teacher;
        $klasses = $teacher->assignedKlasses;
        $subjects = $teacher->assignedSubjects;
        
        return inertia('Teacher/subattendance',[
            'klasses' => $klasses,
            'subjects' => $subjects
        ]);
    }

    public function getStudents(Request $request)
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
public function store(Request $request)
{
    $request->validate([
        'klass_id' => 'required|exists:klasses,id',
        'subject_id' => 'required|exists:subjects,id',
        'date' => 'required|date',
        'attendance' => 'required|array',
        'attendance.*' => 'required|in:present,absent,late,excused',
    ]);

    foreach ($request->attendance as $studentId => $status) {
        SubAttendance::updateOrCreate(
            [
                'student_id' => $studentId,
                'klass_id' => $request->klass_id,
                'subject_id' => $request->subject_id,
                'date' => $request->date,
            ],
            [
                'status' => $status,
            ]
        );
    }

    return redirect(route('teacher.subjectAttendance'));
}

}
