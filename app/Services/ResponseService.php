<?php namespace App\Services;

use App\Services\ApiService;
use Symfony\Component\HttpFoundation\Response as Response;
use Illuminate\Http\Request as Request;
use App;

class ResponseService extends ApiService {

    protected $request;

    protected $json;

    function __construct($json = true)
    {
        $this->json = $json;
    }

    public function returnJson($result)
    {
        $this->json = $result;
    }

    public function response($jsonData, $httpCode, $data = false)
    {
        if(!$data)
            $data = $jsonData;

        if($this->json)
        {
            return response()->json($jsonData, $httpCode);
        }
        else
        {
            return $data;
        }
    }

    public function validationError($errors)
    {
        return $this->response(['error' => $errors], Response::HTTP_CONFLICT);
    }

    public function badRequest($jsonError, $error = false)
    {
        return $this->response($jsonError, Response::HTTP_BAD_REQUEST, $error);
    }

    public function notFound($message = 'Not Found')
    {
        return $this->response(['error' => $message], Response::HTTP_NOT_FOUND);
    }

    public function ok($jsonMessage = 'OK', $message = false)
    {
        return $this->response($jsonMessage, Response::HTTP_OK, $message);
    }

    public function data($data)
    {
        return $this->response(['data' => $data], Response::HTTP_OK);
    }

}
