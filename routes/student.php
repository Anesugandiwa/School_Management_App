<?php
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\controllers\StudentDashboardController;
use App\Http\controllers\StudentProfileController;

Route::group([
    'middleware ' => [App\Http\Middleware\isStudent::class,]
], function(){
    Route::get('/studentDash', [StudentDashboardController::class, 'index'])->name('student_dashboard');
    Route::get('/studentprof', [StudentProfileController::class, 'index'])->name('student_profile');
    Route::get('/student/assignments', [AssignmentController::class, 'getStudentAssignments']);

});

