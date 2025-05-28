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
        $klassSubjectTeacher = Teacher::with('teacher', 'subjects', 'klass');

        return inertia('Admin/subjectTeacher',[
            'klassSubjectTeacher' => $klassSubjectTeacher,
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

    

}
