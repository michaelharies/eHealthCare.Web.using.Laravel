<?php
/*
 * File name: Handler.php
 * Last modified: 2022.04.12 at 12:04:21
 * Author: SmarterVision - https://codecanyon.net/user/smartervision
 * Copyright (c) 2022
 */

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param Throwable $exception
     * @return void
     *
     * @throws Throwable
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     * @param Throwable $exception
     * @return Response
     *
     * @throws Throwable
     */
    public function render($request, Throwable $exception)
    {

        if ($exception instanceof NotFoundHttpException) {
            if ($request->ajax()) {
                return response()->json(['error' => trans('error.page.not_found')], 404);
            }
            return response()->view('vendor.errors.page', ['code' => 404, 'message' => trans('error.page.not_found')]);
        }
        if ($exception instanceof UnauthorizedException) {
            if ($request->ajax()) {
                return response()->json(['error' => $exception->getMessage()], 403);
            }
            return response()->view('vendor.errors.page', ['code' => 403, 'message' => $exception->getMessage()]);
        }
        if ($exception instanceof AuthenticationException) {
            if ($request->ajax()) {
                return response()->json(['error' => $exception->getMessage()], 401);
            }
        }

        return parent::render($request, $exception);
    }
}
