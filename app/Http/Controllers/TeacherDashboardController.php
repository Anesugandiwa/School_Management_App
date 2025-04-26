<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TeacherDashboardController extends Controller
{
    public function index(){
        return inertia('Teacher/display');
    }

    public function klass(){
        return inertia('Teacher/klass');
    }
}
