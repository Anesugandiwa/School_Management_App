<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Marks;

class ReportController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get the student record for the authenticated user
        $student = Student::where('user_id', $user->id)->first();
        
        if (!$student) {
            return inertia('Students/report', [
                'error' => 'No student record found',
                'student' => null,
                'reportData' => [],
                'summary' => null
            ]);
        }
        
        // Get all marks for this student with related data
        $marks = Marks::where('student_id', $student->id)
            ->with(['subject:id,name', 'klass:id,class_name'])
            ->orderBy('subject_id')
            ->get();
        
        // Group marks by subject and calculate averages or get latest marks
        $reportData = [];
        $totalMarks = 0;
        $totalSubjects = 0;
        
        // Group marks by subject
        $groupedMarks = $marks->groupBy('subject_id');
        
        foreach ($groupedMarks as $subjectId => $subjectMarks) {
            $subject = $subjectMarks->first()->subject;
            
            // You can either show all marks or calculate average, or show latest
            // Here I'll calculate the average for each subject
            $avgMark = $subjectMarks->avg('marks');
            $avgTotal = $subjectMarks->avg('total_marks');
            
            // Get the most recent comment
            $latestMark = $subjectMarks->sortByDesc('created_at')->first();
            
            // Calculate percentage
            $percentage = $avgTotal > 0 ? round(($avgMark / $avgTotal) * 100, 1) : 0;
            
            $reportData[] = [
                'subject' => $subject->name,
                'marks' => $avgMark,
                'total_marks' => $avgTotal,
                'percentage' => $percentage,
                'comment' => $latestMark->remarks ?? 'No comment',
                'exam_type' => $latestMark->exam_type ?? 'N/A',
                'grade' => $this->calculateGrade($percentage)
            ];
            
            $totalMarks += $percentage;
            $totalSubjects++;
        }
        
        // Calculate overall average
        $overallAverage = $totalSubjects > 0 ? round($totalMarks / $totalSubjects, 1) : 0;
        
        // Prepare student data with class information
        $studentData = [
            'name' => $student->name . ' ' . $student->surname,
            'class' => $student->klass ? $student->klass->class_name : 'No class assigned',
            'klass_id' => $student->klass_id
        ];
        
        // Summary data
        $summary = [
            'total_subjects' => $totalSubjects,
            'overall_average' => $overallAverage,
            'overall_grade' => $this->calculateGrade($overallAverage),
            'term' => $this->getCurrentTerm(),
            'year' => now()->year,
            'head_remarks' => $this->generateHeadRemarks($overallAverage, $studentData['name'])
        ];
        
        return inertia('Students/report', [
            'student' => $studentData,
            'reportData' => $reportData,
            'summary' => $summary,
            'error' => null
        ]);
    }
    
    private function calculateGrade($percentage)
    {
        if ($percentage >= 90) return 'A+';
        if ($percentage >= 80) return 'A';
        if ($percentage >= 70) return 'B';
        if ($percentage >= 60) return 'C';
        if ($percentage >= 50) return 'D';
        return 'F';
    }
    
    private function getCurrentTerm()
    {
        $month = now()->month;
        
        if ($month >= 1 && $month <= 4) {
            return '1st Term';
        } elseif ($month >= 5 && $month <= 8) {
            return '2nd Term';
        } else {
            return '3rd Term';
        }
    }
    
    private function generateHeadRemarks($average, $studentName)
    {
        if ($average >= 80) {
            return "{$studentName} has demonstrated exceptional academic performance this term. Continue with the excellent work and maintain this outstanding standard.";
        } elseif ($average >= 70) {
            return "{$studentName} has shown good academic progress this term. Keep up the good work and strive for even better results.";
        } elseif ($average >= 60) {
            return "{$studentName} has made satisfactory progress this term. With more effort and dedication, better results can be achieved.";
        } elseif ($average >= 50) {
            return "{$studentName} needs to put in more effort to improve academic performance. Seek help from teachers when needed.";
        } else {
            return "{$studentName} requires significant improvement in academic performance. Additional support and extra study time are strongly recommended.";
        }
    }
}