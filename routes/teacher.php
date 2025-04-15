<?php
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\TeacherDashboardController;
use App\Http\Middleware\isTeacher;

Route::group([
    'prefix' => 'teacher-area',
    'as'    =>'teacher.',
    'middleware' => [App\Http\Middleware\isTeacher::class,]
], function(){
    Route::get('/display', [TeacherDashboardController::class, 'index'])->name('dis');
    
  
});



