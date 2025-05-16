<?php
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\controllers\StudentDashboardController;

Route::group([
    'middleware ' => [App\Http\Middleware\isStudent::class,]
], function(){
    Route::get('/studentDash', [StudentDashboardController::class, 'index'])->name('student_dashboard');

});

