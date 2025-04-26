<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Klass;

class TeacherKlassController extends Controller
{

// public function index(){
//     $user = auth()->user();

//     if (!$user->teacher) {
//         return response()->json([], 200); // Or return an error
//     }

//     $klasses = $user->teacher->klasses;

//     return response()->json($klasses);
// }
 public function index(){
    $klasses = Klass::all();
    return response()->json($klasses);
 }

}
