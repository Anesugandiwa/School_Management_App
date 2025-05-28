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
        $maleCount = Student::where('gender', 'Male')->count();
        $femaleCount = Student::where('gender', 'Female')->count();

        $genderLabels = ['Male', 'Female'];
        $genderDataset = [$maleCount, $femaleCount];


        return inertia('Admin/Dashboard',[
            'subjects' => Subject::count(),
            'teachers' => Teacher::count(),
            'students' => Student::count(),
            'classes' => Klass::count(),

        'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
        'dataset' => [30, 40, 35, 50, 49, 60, 70],

            'studentLabels' => $genderLabels,
            'studentDataset' => $genderDataset,

 
        ]);

    }
}
