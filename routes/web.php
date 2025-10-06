<?php

use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\RegularEmployeeController;
use App\Http\Controllers\OnCallEmployeeController;


Route::resource('employees', App\Http\Controllers\EmployeeController::class)->middleware('auth');
Route::resource('users', App\Http\Controllers\UserController::class)->middleware('auth');
Route::resource('clients', ClientController::class)->middleware(['auth', 'checkRole:2']);

// Separate Employee Management Routes
Route::resource('regular-employees', RegularEmployeeController::class)->middleware('auth');
Route::resource('oncall-employees', OnCallEmployeeController::class)->middleware('auth');

Route::get('/', [AuthController::class, 'showLogin'])->name('login')->middleware('guest');

Route::post('/page', [AuthController::class, 'login'])->name('login_now');

Route::post('/logout', function () {
    Auth::logout();
    return redirect()->route('login');
})->name('logout');


Route::get('/production', [UserController::class, 'showProdHead'])->name('showProdHead')->middleware('auth', 'checkRole:1');
Route::get('/admin', [UserController::class, 'showAdmin'])->name('showAdmin')->middleware('auth', 'checkRole:2');
Route::get('/hr', [UserController::class, 'showAdmin'])->name('go_newPage')->middleware('auth');

// Admin Projects Route
Route::get('/production/projects', [AuthController::class, 'Projects'])->name('ProdHead.projects')->middleware('auth');
Route::post('/projects/{project}/onHold',
    [ProjectController::class, 'onHold']
)->name('onHold')->middleware('auth');



// Project Management Routes
Route::get('projects', [AuthController::class, 'Projects'])->name('projects.index')->middleware('auth');
Route::get('projects/create', [ProjectController::class, 'create'])->name('projects.create')->middleware('auth');
Route::post('projects', [ProjectController::class, 'store'])->name('projects.store')->middleware('auth');
Route::get('projects/{project}', [ProjectController::class, 'show'])->name('projects.show')->middleware('auth');
Route::get('projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit')->middleware('auth');
Route::put('projects/{project}', [ProjectController::class, 'update'])->name('projects.update')->middleware('auth');
Route::delete('projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy')->middleware('auth');
Route::patch('projects/{project}/on-hold', [ProjectController::class, 'onHold'])->name('projects.onHold')->middleware('auth');
Route::patch('projects/{project}/reactivate', [ProjectController::class, 'reactivate'])->name('projects.reactivate')->middleware('auth');
Route::post('projects/{project}/assign-employees', [ProjectController::class, 'assignEmployees'])->name('projects.assignEmployees')->middleware('auth');
Route::delete('projects/{project}/employees/{employee}', [ProjectController::class, 'removeEmployee'])->name('projects.removeEmployee')->middleware('auth');

Route::get('/admin/EmployeeManagement', [UserController::class, 'showAdminEmployeeFunction'])->name('admin.employees.index')->middleware('auth');