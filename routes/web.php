<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/mailable', function () {
    $appraisalRecord = \App\Models\AppraisalRecord::find(3);

    return new \App\Mail\AppraisalPendingReviewMail($appraisalRecord);
});
