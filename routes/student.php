<?php
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\controllers\StudentDashboardController;
use App\Http\controllers\StudentProfileController;
use App\Http\controllers\StudentAssignmentController;
use App\Http\controllers\ReportController;
use App\Http\controllers\StudentActivityController;

Route::group([
    'middleware ' => [App\Http\Middleware\isStudent::class,]
], function(){
    Route::get('/studentDash', [StudentDashboardController::class, 'index'])->name('student_dashboard');
    Route::get('/studentprof', [StudentProfileController::class, 'index'])->name('student_profile');
    // Route::get('/student/assignments', [StudentProfileController::class, 'assignment'])->name('student_assignments');

    Route::get('/studentAssignment', [StudentAssignmentController::class, 'index'])->name('stud_assignment');

    Route::get('/student/assignments/{id}/download', [StudentAssignmentController::class, 'downloadFile'])->name('assignment_download');

    Route::get('/report', [ReportController::class, 'index'])->name('report');

    Route::get('/studentEvent',[StudentActivityController::class, 'index'])->name('student_activity');
    Route::get('/student',[StudentActivityController::class, 'fetchActivities'])->name('activities');


});

