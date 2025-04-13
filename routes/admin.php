<?php
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\AdminDashboardController;

Route::group([
    'middleware' =>[App\Http\Middleware\isAdmin::class,]
], function(){
    Route::get('/Dashboard',[AdminDashboardController::class, 'index'])->name('admin_dash');
});


