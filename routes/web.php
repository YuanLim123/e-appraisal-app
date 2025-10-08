<?php

use Illuminate\Support\Facades\Storage;
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
    $file = $appraisalRecord->getLastMedia('attachments');
    // https://spatie.be/docs/laravel-medialibrary/v11/downloading-media/downloading-a-single-file
    return response()->download($file->getPath(), $file->file_name);
});
