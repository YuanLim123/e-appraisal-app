<?php

use App\Http\Controllers\Api\V1\Auth\RegisteredUserController;
use App\Http\Controllers\Api\V1\UserController;
use App\Http\Controllers\Api\V1\AppraisalControlController;
use App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Middleware\DepartmentMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('register', RegisteredUserController::class);

Route::get('users', [UserController::class, 'index']);

Route::get('appraisal-controls', [AppraisalControlController::class, 'index']);

Route::prefix('admin')->middleware(['auth:sanctum', DepartmentMiddleware::class.':HRA,PAYROLL'])->group(function () {
    Route::post('users', [Admin\UserController::class, 'store']);
});

Route::post('login', LoginController::class);
