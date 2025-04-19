<?php

namespace App\Http\Controllers;
use App\Models\Klass;
use App\Models\Teacher;
use Illuminate\Http\Request;

class KlassController extends Controller
{
    public function index(){
        $klasses = Klass::with('teacher')->get();
        return inertia('Admin/Klass',[
            'klasses' => $klasses,
            'teachers' =>Teacher::all() 
        ]);
    }

    public function create(){

        $teachers = Teacher::all();
        return response()->json($teachers);
        

    }

    public function store(Request $request){
        $request->validate([
            'class_name'    =>'required',
            'year'          =>'required',
            'department'    =>'nullable|in:Science,Arts,Languages,commercials',
            'teachers'      =>'required|exists:teachers,id',
        ]);
        $klass = Klass::firstOrNew(['id' =>$request->id]);

        $klass->class_name = $request->class_name;
        $klass->year = $request->year;
        $klass->department = $request->department;
        $klass->teacher_id = $request->teachers;

        $klass->save();

        

        return redirect(route('admin.Klass.index'));

    }
    public function show(Klass $klass)
    {

    }
    public function update(Request $request, $id){

        $request-> validate([
            'class_name'    =>'required',
            'year'          =>'required',
            'department'    =>'nullable|in:Science,Arts,Languages,commercials',
            'teachers'      =>'required|exists:teachers,id',





        ]);

        $klass =Klass::findOrFail($id);

        $klass->update([
            'class_name' => $request->class_name,
            'year' => $request->year,
            'department' => $request->department,
            'teachers' => $request->teachers,

        ]);

        

            $klass->save();
        

        return redirect()->route('admin.Klass.index')->with('success', 'Class Updated successfully!');


        
    }
    public function destroy(Klass $klass)
    {

        
        $klass->delete();

        return redirect()->back()->with('message', [
            'type'        => 'success',
            'description' => '',
            'title'       => 'record Deleted',
        ]);
    }

}
