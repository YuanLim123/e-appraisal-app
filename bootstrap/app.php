<?php

use App\Exceptions\AgreementRequiredException;
use App\Exceptions\ApproverNotFoundException;
use App\Exceptions\InvalidAppraisalSeasonException;
use App\Exceptions\InvalidRatingSumException;
use App\Exceptions\InvalidWeightAgeException;
use App\Exceptions\RecordAlreadyExistsInSeasonException;
use App\Exceptions\UserHasNoAppraisalCreatedException;
use App\Exceptions\RecordAlreadySubmitException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        apiPrefix: 'api/v1',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (UserHasNoAppraisalCreatedException $e, Request $request) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 422);
            }
        });
        $exceptions->render(function (InvalidAppraisalSeasonException $e, Request $request) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 422);
            }
        });
        $exceptions->render(function (RecordAlreadyExistsInSeasonException $e, Request $request) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 422);
            }
        });
        $exceptions->render(function (InvalidRatingSumException $e, Request $request) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 422);
            }
        });
        $exceptions->render(function (InvalidWeightAgeException $e, Request $request) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 422);
            }
        });
        $exceptions->render(function (AgreementRequiredException $e, Request $request) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 422);
            }
        });
        $exceptions->render(function (RecordAlreadySubmitException $e, Request $request) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 422);
            }
        });
        $exceptions->render(function (ApproverNotFoundException $e, Request $request) {
            if ($request->wantsJson()) {
                return response()->json([
                    'message' => $e->getMessage(),
                ], 404);
            }
        });
    })->create();
