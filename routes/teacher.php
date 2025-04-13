<?php
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::group([
    'middleware' => [App\Http\Middleware\isTeacher::class,]
], function(){
    Route::get('/Dashboard', [TeacherDashboardController::class, 'index'])->name('taecher_dashboard');
});