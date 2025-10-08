<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/mailable', function () {
    $appraisalRecord = \App\Models\AppraisalRecord::find(1);

    return new \App\Mail\AppraisalRecordPendingReviewMail($appraisalRecord);
});

Route::get('file', function () {
    $appraisalRecord = \App\Models\AppraisalRecord::find(1);
    $url = $appraisalRecord->getLastMediaUrl('attachments');
    return response()->download($url);
});
