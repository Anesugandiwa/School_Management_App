<?php
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Middleware\isAdmin;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\KlassController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\KlassSubjectTeacherController;
use App\Http\Controllers\AttendanceReportController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\TimeTableController;



Route::group([
    'prefix' =>'admin',
    'as'     => 'admin.',
    'middleware' =>[App\Http\Middleware\isAdmin::class,]
], function(){
    Route::get('/Dashboard',[AdminDashboardController::class, 'index'])->name('dash');
    Route::resource('/AddTeacher',TeacherController::class, )->parameters([
        'AddTeacher' =>'teacher'
    ]);
    Route::resource('/Klass',KlassController::class, );
    Route::resource('/AddStudent',StudentController::class, )->parameters([
        'AddStudent' => 'student'  ]);
    Route::resource('/Subject',SubjectController::class, );

    Route::resource('/subjectTeacher', KlassSubjectTeacherController::class);
    Route::get('/studAttendence', [AttendanceReportController::class, 'index'])->name('students_attendance');

    Route::resource('/activity', ActivityController::class);
    Route::resource('/timeTable', TimeTableController::class);

    Route::get('/admin/timetable/fetch', [TimetableController::class, 'fetch'])->name('fetchez');
    

 
});




