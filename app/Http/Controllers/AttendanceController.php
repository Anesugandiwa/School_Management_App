<?php

namespace App\Http\Controllers;
use App\Models\Student;
use App\Models\Klass;
use App\Models\Attendance;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request){
        $teacher = auth()->user()->teacher;
        $klasses = $teacher?->klasses ?? [];

        return inertia('Teacher/attendance',[
            'klasses' => $klasses,
            'students' => Student::all(),
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
    public function store(Request $request){
        $request->validate([
            'klass_id' => 'required|exists:klasses,id',
            'date' => 'required|date',
            'records' => 'required|array',
            'records.*.student_id' => 'required|exists:students,id',
            'records.*.status' => 'required|in:present,absent,late',
        ]);
    
        foreach ($request->records as $record) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $record['student_id'],
                    'class_id' => $request->klass_id,
                    'date' => $request->date,
                ],
                [
                    'status' => $record['status'],
                ]
            );
        }
    
        return redirect(route('teacher.get'));
    }
    
}
