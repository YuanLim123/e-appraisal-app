<?php

use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\RegisteredUserController;
use App\Http\Controllers\Api\V1\HR;
use App\Http\Controllers\Api\V1\User\AppraisalController;
use App\Http\Controllers\Api\V1\User\AppraisalRecordController;
use App\Http\Controllers\api\V1\User\ApprovalController;
use App\Http\Controllers\Api\V1\User\UserController;
use App\Http\Middleware\DepartmentMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('login', LoginController::class);

Route::post('register', RegisteredUserController::class);
Route::get('appraisals', [AppraisalController::class, 'index']);
Route::get('users', [UserController::class, 'index']);

Route::middleware(['auth:sanctum'])->group(function () {

    Route::middleware([DepartmentMiddleware::class.':HRA,PAYROLL'])->group(function () {
        Route::post('hr/users', [HR\UserController::class, 'store']);
        Route::post('hr/users/{user}/appraisals', [HR\AppraisalController::class, 'store']);
        Route::put('hr/users/{user}/appraisals/{appraisal}', [HR\AppraisalController::class, 'update']);
        Route::get('hr/appraisal-records', [HR\AppraisalRecordController::class, 'index']);
    });

    Route::get('appraisals/{appraisal}', [AppraisalController::class, 'show'])
        ->middleware('can:view,appraisal');

    Route::post('users/{user}/appraisal-records', [AppraisalRecordController::class, 'store']);
    Route::put('users/{user}/appraisal-records/{appraisalRecord}', [AppraisalRecordController::class, 'update']);

    Route::get('appraisal-records', [AppraisalRecordController::class, 'index']);

    Route::post('users/{user}/appraisal-records/{appraisalRecord}/feedbacks', [AppraisalRecordController::class, 'storeFeedback']);
    Route::post('users/{user}/appraisal-records/{appraisalRecord}/submissions', [AppraisalRecordController::class, 'submit']);

    Route::get('appraisal-records/approvals', [ApprovalController::class, 'index']);
    Route::get('appraisal-records/{appraisalRecord}/approvals', [ApprovalController::class, 'show']);
    Route::post('appraisal-records/{appraisalRecord}/approvals', [ApprovalController::class, 'approve']);
});
