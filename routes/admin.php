<?php
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Middleware\isAdmin;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\KlassController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;


Route::group([
    'prefix' =>'admin',
    'as'     => 'admin.',
    'middleware' =>[App\Http\Middleware\isAdmin::class,]
], function(){
    Route::get('/Dashboard',[AdminDashboardController::class, 'index'])->name('dash');
    Route::resource('/AddTeacher',TeacherController::class, );
    Route::resource('/Klass',KlassController::class, );
    Route::resource('/AddStudent',StudentController::class, );
    Route::resource('/Subject',SubjectController::class, );

 
});




