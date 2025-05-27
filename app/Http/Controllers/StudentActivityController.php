<?php
// app/Http/Controllers/ActivityController.php

namespace App\Http\Controllers;

use App\Models\Activity;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentActivityController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $activities =Activity::all();
        
        return inertia('Students/studentEvent', [
            'activities' => $activities,
            
        ]);
    }

    public function show(Activity $activity)
    {
        $activity->load(['creator:id,name']);
        
        $userRegistration = null;
        $canRegister = false;

        if (auth()->user()->role === 'student') {
            $student = auth()->user()->student;
            if ($student) {
                $userRegistration = ActivityRegistration::where('activity_id', $activity->id)
                                                      ->where('student_id', $student->id)
                                                      ->first();
                $canRegister = $activity->can_register && !$userRegistration;
            }
        }

        return inertia('Activities/Show', [
            'activity' => $activity,
            'userRegistration' => $userRegistration,
            'canRegister' => $canRegister,
            'registrationCount' => $activity->registrations()->count()
        ]);
    }

    public function register(Request $request, Activity $activity)
    {
        $user = auth()->user();
        
        if ($user->role !== 'student') {
            return response()->json(['message' => 'Only students can register'], 403);
        }

        $student = $user->student;
        if (!$student) {
            return response()->json(['message' => 'Student record not found'], 404);
        }

        if (!$activity->can_register) {
            return response()->json(['message' => 'Registration not available'], 400);
        }

        // Check if already registered
        $existingRegistration = ActivityRegistration::where('activity_id', $activity->id)
                                                   ->where('student_id', $student->id)
                                                   ->first();

        if ($existingRegistration) {
            return response()->json(['message' => 'Already registered'], 400);
        }

        ActivityRegistration::create([
            'activity_id' => $activity->id,
            'student_id' => $student->id,
            'registered_at' => now(),
            'notes' => $request->notes
        ]);

        return response()->json(['message' => 'Registered successfully']);
    }

    public function unregister(Activity $activity)
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            return response()->json(['message' => 'Student record not found'], 404);
        }

        ActivityRegistration::where('activity_id', $activity->id)
                           ->where('student_id', $student->id)
                           ->delete();

        return response()->json(['message' => 'Unregistered successfully']);
    }

    public function fetchActivities(){
        return response()->json(Activity::all());
    }
}
