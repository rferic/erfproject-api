<?php

namespace App\Exceptions;

use App\Http\Utils\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    use ApiResponse;
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
     * Render an exception into an HTTP response.
     *
     * @param  Request  $request
     * @param Throwable $exception
     * @return Response
     *
     * @throws Throwable
     */
    public function render ( $request, Throwable $exception )
    {
        if ($exception instanceof \HttpException) {
            return $this->responseFail([], $exception->getMessage(), $exception->status);
        }

        if ($exception instanceof AuthenticationException) {
            return $this->responseFail([], $exception->getMessage(), $exception->status);
        }

        if ($exception instanceof ValidationException) {
            return $this->responseFail($exception->errors(), $exception->getMessage(), $exception->status);
        }

        return parent::render($request, $exception);
    }
}
