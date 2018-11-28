<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ApiController extends Controller {

    protected $statusCode = 200;

    /**
     * get current http status code
     * @return [integer] [Http Status Code]
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * set current http status code
     * @param [integer] $statusCode [valid http status code]
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * Simpe 404 not found json response
     * @param  [string] $msg [casted message with the response]
     * @return [mixed]       [Http Json Response]
     */
    public function respondNotFound($msg = 'Not Found')
    {
        return $this->setStatusCode(404)->respondWithError($msg);
    }
    /**
     * simple 401 not Authenticated response
     * @param  [string] $msg
     * @return [mixed]       [Http Json Response]
     */
    public function respondNotAuthenticated($msg = 'Not Authenticated')
    {
        return $this->setStatusCode(401)->respondWithError($msg);
    }


    /**
     * simple 403 not Authorized response
     * @param  [string] $msg
     * @return [mixed]       [Http Json Response]
     */
    public function respondNotAuthorized($msg = 'Not Authorized')
    {
        return $this->setStatusCode(403)->respondWithError($msg);
    }


    /**
     * simple 406 not Acceptable response
     * @param  [string] $msg
     * @return [mixed]       [Http Json Response]
     */
    public function respondNotAcceptable($msg = 'Not Acceptable')
    {
        return $this->setStatusCode(406)->respondWithError($msg);
    }

    /**
     * Main Error Response method to handle error values and error messages
     * @param  [string] $msg [text to describe the error]
     * @return [mixed]       [Http Json Response]
     */
    public function respondWithError($msg)
    {
        return $this->respond(["success" => false, "msg" => $msg]);
    }


    /**
     * [respondCreated description]
     * @return [type] [description]
     */
    public function respondCreated($data = [])
    {
        return $this->setStatusCode(201)->respondSuccess($data);
    }

    /**
     * [respondAccepted description]
     * @return [type] [description]
     */
    public function respondAccepted($data = [])
    {
        return $this->setStatusCode(202)->respondSuccess($data);
    }


    /**
     * [respondOK description]
     * @return [type] [description]
     */
    public function respondOK($data = [])
    {
        return $this->setStatusCode(200)->respondSuccess($data);
    }


    /**
     * Main Successful response to handle data sent
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    public function respondSuccess($data = [])
    {
        if(!empty($data))
            return $this->respond(["success" => true, "data"=> $data]);
        else
            return $this->respond(["success" => true]);
    }

    /**
     * abstract respond method
     * @param  [mixed] 	$data    [data to be encapsulated in the response]
     * @param  [array]  $headers [any additonal headers to be sent with the response]
     * @return [mixed]           [Http Json Response]
     */
    public function respond($data , $headers = [])
    {
        return Response::json($data , $this->getStatusCode(), $headers);
    }

}
