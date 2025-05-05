<?php
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\TeacherDashboardController;
use App\Http\Controllers\TeacherKlassController;
use App\Http\Controllers\TeacherSubjectController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MarksController;
use App\Http\Controllers\TeacherController;
use App\Http\Middleware\isTeacher;

Route::group([
    'prefix' => 'teacher-area',
    'as'    =>'teacher.',
    'middleware' => [App\Http\Middleware\isTeacher::class,]
], function(){
    Route::get('/display', [TeacherDashboardController::class, 'index'])->name('dis');
    Route::get('/teacher/klasses', [TeacherKlassController::class, 'index'])->name('klasses.respo');
    Route::get('/klass', [TeacherDashboardController::class, 'klass'])->name('klass');
    Route::get('/subjects', [TeacherSubjectController::class, 'index'])->name('teachersub');
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/addmarks', [MarksController::class, 'index'])->name('addmarks');
    // Route::post('/addmarks', [MarksController::class, 'fetchStudents'])->name('fetchStudents');

    Route::post('/addmarks', [StudentController::class, 'fetchStudents'])
    ->name('fetchStudents');
    Route::get('/fetch-students', [MarksController::class, 'fetchStudents'])->name('fetchStudents');
  
});



