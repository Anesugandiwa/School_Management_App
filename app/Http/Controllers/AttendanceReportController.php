<?php

namespace App\Http\Controllers;
use App\Models\Attendance;
use App\Models\Klass;
use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

class AttendanceReportController extends Controller

{
    public function index(Request $request){
       
        $date = $request->input('date', now()->toDateString());
        
        // Get all classes
        $classes = Klass::all();
        
        // Prepare data for the chart
        $classLabels = [];
        $presentCounts = [];
        $absentCounts = [];
        $lateCounts = [];
        
        foreach ($classes as $class) {
            // Add class name to labels
            $classLabels[] = $class->class_name;
            
            // Count attendance statuses for this class on the selected date
            $stats = Attendance::where('class_id', $class->id)
                ->where('date', $date)
                ->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();
            
            // Get counts for each status (default to 0 if not found)
            $presentCounts[] = $stats['present'] ?? 0;
            $absentCounts[] = $stats['absent'] ?? 0;
            $lateCounts[] = $stats['late'] ?? 0;
        }
        
        // Get overall attendance stats for the selected date
        $totalStudents = Attendance::where('date', $date)->count();
        $totalPresent = Attendance::where('date', $date)->where('status', 'present')->count();
        $totalAbsent = Attendance::where('date', $date)->where('status', 'absent')->count();
        $totalLate = Attendance::where('date', $date)->where('status', 'late')->count();
        
        $attendanceRate = $totalStudents > 0 
            ? round(($totalPresent + $totalLate) / $totalStudents * 100, 1) 
            : 0;
        
        return inertia('Admin/studAttendence', [
            'date' => $date,
            'classLabels' => $classLabels,
            'presentCounts' => $presentCounts,
            'absentCounts' => $absentCounts,
            'lateCounts' => $lateCounts,
            'stats' => [
                'total' => $totalStudents,
                'present' => $totalPresent,
                'absent' => $totalAbsent,
                'late' => $totalLate,
                'attendanceRate' => $attendanceRate
            ]
        ]);
    }
}



