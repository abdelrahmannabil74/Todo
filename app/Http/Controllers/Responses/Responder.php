<?php

namespace App\Http\Controllers\Responses;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

interface  Responder
{
    public function respond($data);
    public function respondWithError($error);
    public function respondWithValidationError($error);
    public function respondWithAuthenticationError($error = 'Forbidden!');
    //
}
