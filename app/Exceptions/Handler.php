<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use CloudCreativity\LaravelJsonApi\Exceptions\HandlesErrors;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;
use Throwable;
use Exception;

class Handler extends ExceptionHandler
{
    use HandlesErrors;
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
     * @throws Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  Request  $request
     * @param Throwable $exception
     * @return Response
     *
     * @throws Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($request->wantsJson() || $this->isJsonApi($request, $exception)) {
            return $this->renderJsonApi($request, $exception);
        }

        return parent::render($request, $exception);
    }
}
