<?php namespace App\Services;

use App\Services\ApiService;
use Symfony\Component\HttpFoundation\Response as Response;
use Illuminate\Http\Request as Request;
use App;

class ResponseService extends ApiService
{
    protected $request;

    protected $json;

    public function response($data, $httpCode)
    {
        return response()->json($data, $httpCode);
    }

    public function validationError($errors)
    {
        return $this->response(['error' => $errors], Response::HTTP_CONFLICT);
    }

    public function badRequest($error)
    {
        return $this->response($error, Response::HTTP_BAD_REQUEST);
    }

    public function notFound($message = 'Not Found')
    {
        return $this->response(['error' => $message], Response::HTTP_NOT_FOUND);
    }

    public function ok($message = 'OK')
    {
        return $this->response($message, Response::HTTP_OK);
    }

    public function data($data)
    {
        return $this->response(['data' => $data], Response::HTTP_OK);
    }
}
