<?php

use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\RegisteredUserController;
use App\Http\Controllers\Api\V1\Auth\LogoutController;
use App\Http\Controllers\Api\V1\HR;
use App\Http\Controllers\Api\V1\User;
use App\Http\Middleware\DepartmentMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('login', LoginController::class);

Route::post('register', RegisteredUserController::class);
Route::get('appraisals', [User\AppraisalController::class, 'index']);
Route::get('users', [User\UserController::class, 'index']);

Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('logout', LogoutController::class);

    Route::middleware([DepartmentMiddleware::class . ':HRA,PAYROLL'])->group(function () {
        Route::post('hr/users', [HR\UserController::class, 'store']);
        Route::post('hr/appraisals', [HR\AppraisalController::class, 'store']);
        Route::put('hr/appraisals/{appraisal}', [HR\AppraisalController::class, 'update']);
        Route::get('hr/appraisal-records', [HR\AppraisalRecordController::class, 'index']);
    });

    Route::get('appraisals/{appraisal}', [User\AppraisalController::class, 'show'])->middleware('can:view,appraisal');

    Route::get('appraisal-records', [User\AppraisalRecordController::class, 'index']);
    Route::post('appraisal-records', [User\AppraisalRecordController::class, 'store']);
    Route::put('appraisal-records/{appraisalRecord}', [User\AppraisalRecordController::class, 'update']);

    Route::post('appraisal-records/{appraisalRecord}/feedbacks', User\AppraisalRecordFeedbackController::class);
    Route::post('appraisal-records/{appraisalRecord}/submissions', User\AppraisalRecordSubmitController::class);

    Route::get('appraisal-records/approvals', [User\ApprovalController::class, 'index']);
    Route::get('appraisal-records/{appraisalRecord}/approvals', [User\ApprovalController::class, 'show']);
    Route::post('appraisal-records/{appraisalRecord}/approvals', [User\ApprovalController::class, 'store']);
    Route::delete('appraisal-records/{appraisalRecord}/rejects', [User\ApprovalController::class, 'destroy']);

    Route::post('appraisal-records/{appraisalRecord}/attachments', [User\AppraisalRecordAttachmentController::class, 'store']);
    Route::delete('appraisal-records/{appraisalRecord}/attachments', [User\AppraisalRecordAttachmentController::class, 'destroy']);
});
