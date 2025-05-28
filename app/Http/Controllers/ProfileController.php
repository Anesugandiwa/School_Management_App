<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Teacher;

class ProfileController extends Controller
{
    public function index(){
        $teacher = Teacher::where('user_id', auth()->id())->with('subjects')->first();

        return inertia('Teacher/profile',[
            'teacher' => $teacher
        ]);
    }
}
