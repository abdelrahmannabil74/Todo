<?php

namespace App\Http\Controllers\Responses;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ApiResponder implements Responder
{

    /**
     * @var int
     */
    private $status = 200;


    /**
     * @param $status
     *
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @param $data
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function respond($data)
    {
        return response()->json($data, $this->status);
    }

    /**
     * @param $error
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function respondWithError($error)
    {
        return $this->respond(['errors' => $error], $this->status);
    }

    /**
     * @param $error
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function respondWithValidationError($error)
    {
        return $this->setStatus(422)->respondWithError($error);
    }

    /**
     * @param string $error
     *
     * @return \Illuminate\Http\JsonResponse
     */

    public function respondWithAuthenticationError($error = 'Forbidden!')
    {
        return $this->setStatus(401)->respondWithError($error);
    }

}
