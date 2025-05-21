<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\Assignment;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentAssignmentController extends Controller
{
public function index(){
    $user = auth()->user();
    
    // First try to find the student by user_id
    $student = Student::where('user_id', $user->id)->first();
    
    if (!$student) {
        // If no student is found, check if we need to update an existing record
        // This is a fallback for when student records exist but aren't linked to users
        if ($user->role === 'student') {
            // Try to find a student with matching name (assuming name format is consistent)
            $possibleStudent = Student::where('name', explode(' ', $user->name)[0])
                                     ->first();
            
            if ($possibleStudent) {
                // Link the student to the user
                $possibleStudent->user_id = $user->id;
                $possibleStudent->save();
                $student = $possibleStudent;
            }
        }
        
        // If we still don't have a student record
        if (!$student) {
            return inertia('Students/studentAssignment', [
                'assignments' => [],
                'error' => 'No student record found'
            ]);
        }
    }
    
    // The rest of your controller remains the same
    
    // Check if student is associated with a class
    if (!$student->klass_id) {
        return inertia('Students/studentAssignment', [
            'assignments' => [],
            'error' => 'No class assigned'
        ]);
    }
    
    // Eager load assignments with their related klass and subject
    $assignments = Assignment::with(['klass', 'subject'])
        ->where('klass_id', $student->klass_id)
        ->orderByDesc('created_at')
        ->get();
    
    // Format assignments for frontend
    $formattedAssignments = $assignments->map(function ($assignment) {
        return [
            'id' => $assignment->id,
            'title' => $assignment->title,
            'description' => $assignment->description,
            'due_date' => $assignment->due_date,
            'file_path' => $assignment->file_path, // Add this line
            'has_file' => !empty($assignment->file_path),
            'klass' => [
                'id' => $assignment->klass->id,
                'klass_id' => $assignment->klass->class_name // Using class_name for display
            ],
            'subject' => [
                'id' => $assignment->subject->id,
                'name' => $assignment->subject->name
            ]
        ];
    });
    
    return inertia('Students/studentAssignment', [
        'assignments' => $formattedAssignments
    ]);
    }
    
    public function downloadFile($id)
    {
        $assignment = Assignment::findOrFail($id);
        
        // Check if student is allowed to access this assignment
        
        $user = auth()->user();
        $student = Student::where('user_id', $user->id)->first();
        
        if (!$student || $student->klass_id != $assignment->klass_id) {
            
            abort(403, 'You do not have permission to access this file');
        }
        
        if (!$assignment->file_path) {
            abort(404, 'No file available for this assignment');
        }
        
        $path = storage_path('app/public/' . $assignment->file_path);
        
        if (!file_exists($path)) {
            abort(404, 'File not found');
        }
        
        return response()->download($path);
}
    
}
