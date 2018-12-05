<?php

namespace App\Exceptions;

use App\Http\Controllers\Responses\Responder;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;

class ApiHandler extends ExceptionHandler
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


    private $responder;

    public function __construct(Container $container,Responder $responder)
    {
        parent::__construct($container);

        $this->responder=$responder;

    }

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {

        return parent::render($request, $exception);

//        if ($exception instanceof ValidationException){
//            return response()->json(['error' => $exception->getMessage()])->setStatusCode(406);
//        }
//
//        return response()->json(['error' => $exception->getMessage()])->setStatusCode(401);
//        return parent::render($request, $exception);
////        return response()->json(
////            [
////                'errors' => [
////                    'status' => 401,
////                    'message' => 'Unauthenticated',
////                ]
////            ], 401
////        );



    }


    protected function unauthenticated($request, AuthenticationException $exception)
    {
        return $this->responder->respondWithAuthenticationError();
    }
    /**
     * Create a response object from the given validation exception.
     *
     * @param  \Illuminate\Validation\ValidationException  $e
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function convertValidationExceptionToResponse(ValidationException $e, $request)
    {
        return $this->responder->respondWithValidationError($e->errors());
    }

}
