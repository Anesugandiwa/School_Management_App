<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;
use App\Models\Subject;
use App\Models\Klass;
use App\Models\KlassSubjectTeacher;
class KlassSubjectTeacherController extends Controller
{
    public function index(){
        $klassSubjectTeacher = KlassSubjectTeacher::with('teacher', 'subject', 'klass')->get();
        
        $transformedData = $klassSubjectTeacher->map(function ($item) {
            return [
                'id' => $item->id, // Include the ID for edit/delete operations
                'class_name' => $item->klass->class_name,
                'subject_name' => $item->subject->name,
                'teacher_name' => $item->teacher->first_name . ' ' . $item->teacher->last_name,
                // Include original item for editing
                'teacher_id' => $item->teacher_id,
                'subject_id' => $item->subject_id,
                'klass_id' => $item->klass_id,
            ];
        });
        



        return inertia('Admin/subjectTeacher',[
            'klassSubjectTeacher' => $transformedData,
            'teachers' => Teacher::all(),
            'subjects' => Subject::all(),
            'klasses' => Klass::all(),
        
        ]);
    }
    public function store(Request $request)
{
    $request->validate([
        'klasses' => 'required|array',
        'klasses.*' => 'exists:klasses,id',
        'subjects' => 'required|array',
        'subjects.*' => 'exists:subjects,id',
        'teachers' => 'required|exists:teachers,id',
    ]);

    foreach ($request->klasses as $klass_id) {
        foreach ($request->subjects as $subject_id) {
            KlassSubjectTeacher::updateOrCreate(
                [
                    'klass_id' => $klass_id,
                    'subject_id' => $subject_id,
                    'teacher_id' => $request->teachers,
                ],
                []
            );
        }
    }

    return redirect(route('admin.subjectTeacher.index'));
}
public function update(Request $request, $id)
{
    $request->validate([
        'klasses' => 'required|array',
        'klasses.*' => 'exists:klasses,id',
        'subjects' => 'required|array', 
        'subjects.*' => 'exists:subjects,id',
        'teachers' => 'required|exists:teachers,id',
    ]);

    // 💡 Retrieve the record
    $subjectTeacher = KlassSubjectTeacher::findOrFail($id);

    // 💡 Update the record
    $subjectTeacher->update([
        'klass_id' => $request->klasses[0], 
        'subject_id' => $request->subjects[0], 
        'teacher_id' => $request->teachers,
    ]);

    return redirect(route('admin.subjectTeacher.index'));
}



    public function destroy(KlassSubjectTeacher $subjectTeacher)
    {
        $subjectTeacher->delete();
        
        return redirect(route('admin.subjectTeacher.index'));
    }

    

}
