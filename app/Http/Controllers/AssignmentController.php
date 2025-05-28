<?php

namespace App\Http\Controllers;
use App\Models\Assignment;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index(Request $request)
    {
        $teacher = auth()->user()->teacher;
        $klasses = $teacher->assignedKlasses;
        $subjects = $teacher->assignedSubjects;
    
        return inertia('Teacher/assignment', [
            'klasses' => $klasses,
            'subjects' => $subjects,
           
        ]); 
    }

    public function store(Request $request){
        $request->validate([
            'klass_id' => 'required|exists:klasses,id',
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
            'file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx|max:10240',
        ]);
        


        $assignment = Assignment::firstOrnew(['id'=>$request->id]);
        $assignment->klass_id = $request->klass_id;
        $assignment->subject_id = $request->subject_id;
        $assignment->title = $request->title;
        $assignment->description = $request->description;
        $assignment->due_date = $request->due_date;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('assignments', $fileName, 'public');
            $assignment->file_path = $filePath;
        }

        $assignment->save();

        return redirect(route('teacher.assignment'));


    }
}
