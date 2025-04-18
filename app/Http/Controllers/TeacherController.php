<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;

class TeacherController extends Controller
{
    public function index(){
        $teachers = Teacher::all();
        return inertia('Admin/AddTeacher',[
            'teachers' => $teachers,
        ]);
    }

    public function create(){

    }

    public function store(Request $request){
        $request->validate([
            'first_name'    =>'required|string|min:3|max:225',
            'last_name'     => 'required|string|min:3|max:225',
            'gender'        => 'required|in:Male,Female,Other',
            'Date_Of_Birth' =>'required|date|before:today',
            'national_id'   =>'required|string|min:6|max:20|unique:teachers,national_id',
            'email'         =>'required|email|max:255|unique:teachers,email',
            'phone'         =>'required|string|min:10|max:15|unique:teachers,phone',
            'address'       =>'required|string|max:500',
            'department'    =>'nullable|in:Science,Arts,Languages,commercials',
            'password'      =>'nullable|string|min:8',

        ]);
        $teacher = Teacher::firstOrNew(['id' =>$request->id]);

        $teacher->first_name          = $request->first_name;
        $teacher->last_name           = $request->last_name;
        $teacher->gender              = $request->gender;
        $teacher->Date_Of_Birth       = $request->Date_Of_Birth;
        $teacher->national_id         = $request->national_id;
        $teacher->email               = $request->email;
        $teacher->phone               = $request->phone;
        $teacher->address             = $request->address;
        $teacher->department          = $request->department;
        $teacher->password             = $request->password;


        $teacher->save();

        return redirect(route('admin.AddTeacher.index'));



    }
    public function show(Teacher $teacher)
    {

    }
    public function update(Request $request, $id){

        $request-> validate([
            'first_name'    =>'required|string|min:3|max:225',
            'last_name'     => 'required|string|min:3|max:225',
            'gender'        => 'required|in:Male,Female,Other',
            'Date_Of_Birth' =>'required|date|before:today',
            'national_id'   =>'required|string|min:6|max:20|unique:teachers,national_id',
            'email'         =>'required|email|max:255|unique:teachers,email',
            'phone'         =>'required|string|min:10|max:15|unique:teachers,phone',
            'address'       =>'required|string|max:500',
            'department'    =>'nullable|in:Science,Arts,Languages,commercials',
            'password'      =>'nullable|string|min:8',





        ]);

        $teacher =Teacher::findOrFail($id);

        $teacher->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'gender' => $request->gender,
            'Date_Of_Birth' => $request->Date_Of_Birth,
            'national_id' => $request->national_id,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'department' => $request->department,
            'password' => $request->password,
        ]);

        

            $teacher->save();
        

        return redirect()->route('admin.AddTeacher.index')->with('success', 'Teacher Updated successfully!');


        
    }
    public function destroy(Teacher $teacher)
    {

        
        $teacher->delete();

        return redirect()->back()->with('message', [
            'type'        => 'success',
            'description' => '',
            'title'       => 'record Deleted',
        ]);
    }
}
