<?php
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\controllers\StudentDashboardController;
use App\Http\controllers\StudentProfileController;
use App\Http\controllers\StudentAssignmentController;

Route::group([
    'middleware ' => [App\Http\Middleware\isStudent::class,]
], function(){
    Route::get('/studentDash', [StudentDashboardController::class, 'index'])->name('student_dashboard');
    Route::get('/studentprof', [StudentProfileController::class, 'index'])->name('student_profile');
    // Route::get('/student/assignments', [StudentProfileController::class, 'assignment'])->name('student_assignments');

    Route::get('/studentAssignment', [StudentAssignmentController::class, 'index'])->name('stud_assignment');

});

