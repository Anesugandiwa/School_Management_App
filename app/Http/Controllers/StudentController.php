<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\klass;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $students = Student::with('klass')->get();
        return inertia('Admin/AddStudent',[
            'students' => $students,
            'klasses' => klass::all(),

        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([

            'name' => "required|min:1|max:225",
            'surname' => "required|min:1|max:225",
            'gender' =>"required|in:Male,Female",
            'date_of_birth' => "required",
            'address' => "required",
            'contact' => "required",
            'nationality' =>"required",
            'klasses' => "required|exists:klasses,id",
        ]);

        $plainPassword = $request->name;

        $name = strtolower($request->name);
        $emailName = preg_replace('/\s+/','',$name);
        $email = $emailName. '@gmail.com';

        $user = User::create([
            'name' => $request->name . ' ' . $request->surname,
            'email' => $email,
            'email_verified_at' => now(),
            'password' => Hash::make($plainPassword),
            'role' => 'student',
            
        ]);
        
        $student = new Student();
        // $student->user_id = $user->id;

        $student->name              = $request->name;
        $student->surname           = $request->surname;
        $student->gender            = $request->gender;
        $student->date_of_birth     = $request->date_of_birth;
        $student->address           = $request->address;
        $student->contact           = $request->contact;
        $student->nationality       = $request->nationality;
        $student->klass_id             = $request->klasses;

        $student->save();

        return redirect(route('admin.AddStudent.index'));
    }

    public function fetchStudents(Request $request)
    {
    $request->validate([
        'klass_id' => 'required|exists:klasses,id',
        'subject_id' => 'required|exists:subjects,id',
    ]);

    $klass = Klass::with(['student'])->find($request->klass_id);

    // Get students in that class
    $students = $klass->student->map(function ($student) {
        return [
            'id' => $student->id,
            'name' => $student->first_name,
            // optionally return existing mark if you track marks elsewhere
        ];
    });

    return response()->json($students);
}

    /**
     * Display the specified resource.
     */
    public function show(Student $student)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([

            'name' => "required|min:1|max:225",
            'surname' => "required|min:1|max:225",
            'gender' =>"required|in:Male,Female",
            'date_of_birth' => "required",
            'address' => "required",
            'contact' => "required",
            'nationality' =>"required",
            'klass' => "nullable",
        ]);
        $student =Student::findOrFail($id);

        $student->update([
            'name' => $request->name,
            'surname' =>$request->surname,
            'gender' =>$request->gender,
            'date_of_birth' =>$request->date_of_birth,
            'address' =>$request->address,
            'contact' =>$request->contact,
            'nationality' =>$request->nationality,
            'klass' =>$request->klass,
        ]);

        $student->save();
        return redirect(route('admin.AddStudent.index'))->with('success', 'Student has been updated successfully!', 'success');
        
        
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        $student->delete();
    
        return redirect()->back()->with('message', [
            'type'        => 'success',
            'description' => '',
            'title'       => 'Record Deleted',
        ]);
    }
    
}
