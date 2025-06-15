<?php

namespace App\Http\Controllers;
use App\Models\SubAttendance;
use App\Models\Subject;
use App\Models\User;
use App\Models\Klass;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentSubAttendanceController extends Controller
{
private function getAuthenticatedStudent()
{
    $user = auth()->user();
    return Student::where('user_id', $user->id)->first();
}

/**
 * Display student attendance dashboard
 */
public function attendanceIndex()
{
    $student = $this->getAuthenticatedStudent();
    
    if (!$student || !$student->klass_id) {
        return inertia('Students/subjectattendance', [
            'error' => 'No class assigned to your account.',
            'student' => null,
            'klass' => null
        ]);
    }

    $klass = Klass::find($student->klass_id);

    return inertia('Students/subjectattendance', [
        'student' => $student,
        'klass' => $klass,
        // 'currentTerm' => $this->getCurrentTerm(),
        // 'academicYear' => $this->getCurrentAcademicYear(),
        'error' => null
    ]);
}

/**
 * Get attendance statistics for the authenticated student
 */
public function getAttendanceStats(Request $request)
{
    $student = $this->getAuthenticatedStudent();
    
    if (!$student || !$student->klass_id) {
        return response()->json([
            'error' => 'No class assigned to your account.',
            'data' => null
        ], 400);
    }

    $request->validate([
        'start_date' => 'nullable|date',
        'end_date' => 'nullable|date'
    ]);

    // Get attendance data for this student (using student.id, not user.id)
    $query = SubAttendance::where('student_id', $student->id)
        ->where('klass_id', $student->klass_id)
        ->with(['subject:id,name']);

    // Apply filters if provided
    if ($request->start_date) {
        $query->where('date', '>=', $request->start_date);
    }
    
    if ($request->end_date) {
        $query->where('date', '<=', $request->end_date);
    }

    $attendanceRecords = $query->get();

    // Group by subject and calculate statistics
    $subjectStats = $attendanceRecords->groupBy('subject_id')->map(function ($records, $subjectId) {
        $subject = $records->first()->subject;
        $totalClasses = $records->count();
        $presentCount = $records->where('status', 'present')->count();
        $absentCount = $records->where('status', 'absent')->count();
        $lateCount = $records->where('status', 'late')->count();
        $excusedCount = $records->where('status', 'excused')->count();
        
        $attendancePercentage = $totalClasses > 0 ? round(($presentCount / $totalClasses) * 100, 1) : 0;

        return [
            'subject_id' => $subjectId,
            'subject_name' => $subject->name ?? 'Unknown Subject',
            'total_classes' => $totalClasses,
            'present' => $presentCount,
            'absent' => $absentCount,
            'late' => $lateCount,
            'excused' => $excusedCount,
            'attendance_percentage' => $attendancePercentage,
            'recent_records' => $records->sortByDesc('date')->take(5)->map(function($record) {
                return [
                    'status' => $record->status
                ];
            })->values()
        ];
    })->values();

    // Overall statistics
    $overallStats = [
        'total_subjects' => $subjectStats->count(),
        'total_classes_attended' => $attendanceRecords->where('status', 'present')->count(),
        'total_classes_held' => $attendanceRecords->count(),
        'overall_attendance_percentage' => $attendanceRecords->count() > 0 
            ? round(($attendanceRecords->where('status', 'present')->count() / $attendanceRecords->count()) * 100, 1) 
            : 0
    ];

    $klass = Klass::find($student->klass_id);

    return response()->json([
        'success' => true,
        'data' => [
            'subject_statistics' => $subjectStats,
            'overall_statistics' => $overallStats,
            'student' => [
                'id' => $student->id,
                'name' => $student->name,
                'surname' => $student->surname ?? '',
                'class_name' => $klass->class_name ?? 'Unknown'
            ]
        ]
    ]);
}
}
