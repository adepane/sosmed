<?php

namespace App\Exceptions;

use App\Helper\Helper;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (NotFoundHttpException $notFoundHttpException, Request $request) {
            if ($request->is('api/*')) {
                return (new Helper)->apiResponse(false, ['errorCode' => 404], 'Endpoint not found.');
            }
        });
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return (new Helper)->apiResponse(false, ['errorCode' => 401], 'Unauthenticated');
    }
}
