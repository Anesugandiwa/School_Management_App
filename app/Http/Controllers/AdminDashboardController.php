<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Klass;

class AdminDashboardController extends Controller
{
    public function index(){
        return inertia('Admin/Dashboard',[
            'subjects' => Subject::count(),
            'teachers' => Teacher::count(),
            'students' => Student::count(),
            'classes' => Klass::count(),
        ]);
    }
}
