<?php
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\TeacherDashboardController;
use App\Http\Controllers\TeacherKlassController;
use App\Http\Controllers\TeacherSubjectController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MarksController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\TeacherTimeTableController;
use App\Http\Controllers\SubAttendanceController;
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
    Route::post('/addmarks', [MarksController::class, 'store'])->name('storemarks');
    

    // Attendence Routes
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('get');
    Route::get('/get-students', [AttendanceController::class, 'getStudents'])->name('getStudents');
    Route::post('/attendance', [AttendanceController::class, 'store'])->name('addAttendance');
    
    // Assignment Routes
    Route::get('/assignment', [AssignmentController::class, 'index'])->name('assignment');
    Route::post('/assignment', [AssignmentController::class, 'store'])->name('uploadAssignment');


    // timetable routes
    Route::get('/timetable', [TeacherTimeTableController::class, 'index'])->name('timetable.index');
    Route::get('/timetable/fetch', [TeacherTimeTableController::class, 'fetchTimetable'])->name('timetable.fetch');

    // Subject Attendance routes

    Route::get('/subattendance', [SubAttendanceController::class, 'index'])->name('subjectAttendance');
    Route::get('/subattendance/fetch', [SubAttendanceController::class, 'getStudents'])->name('getStudents');
    Route::post('/subattendance/store', [SubAttendanceController::class, 'store'])->name('store_attendance');

    Route::get('/teacher/attendance-percentage', [TeacherDashboardController::class, 'getAttendanceRate'])->name('percentage');
    Route::get('/teacher/count', [TeacherDashboardController::class, 'studentCount'])->name('count');
});



