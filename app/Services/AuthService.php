<?php namespace App\Services;

use Validator;
use App\Services\ApiService;
use App\Services\ResponseService;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\JWTAuth as JWTAuth;

class AuthService extends ApiService {

    protected $jwtauth;

    protected $responseService;

    protected $rules = [
        'email' => 'required',
        'password' => 'required'
    ];

    public function validator(array $data)
    {
        return Validator::make($data, $this->rules);
    }

    function __construct(JWTAuth $jwtauth, ResponseService $responseService)
    {
        $this->jwtauth = $jwtauth;
        $this->responseService = $responseService;
    }

    public function getAuthUser($request)
    {

        if (!$token = $this->jwtauth->setRequest($request)->getToken()) {
            return response()->json(['error' => 'Could not get user token'], 400);
        }

        try {
            $user = $this->jwtauth->authenticate($token);
        } catch (TokenExpiredException $e) {
            return response()->json(['error' => 'User token is expired'], 400);
        } catch (JWTException $e) {
            return response()->json(['error' => 'User token is invalid'], 400);
        }

        return response()->json(['data' => $user], 200);
    }

    public function login(array $data)
    {
        $validation = $this->validator($data);
        if($validation->fails())
        {
            return $this->responseService->validationError($validation->errors()->all());
        }
        else
        {
            $credentials = array(
                "email" => $data['email'],
                "password" => $data['password']
            );

            if (!$token = $this->jwtauth->attempt($credentials)) {
                return response()->json(false, 401);
            }

            return response()->json(compact('token'));        
        }
    }

    public function adminLogin(array $data)
    {
        $validation = $this->validator($data);
        if($validation->fails())
        {
            return $this->responseService->validationError($validation->errors()->all());
        }
        else
        {
            $credentials = array(
                "email" => $data['email'],
                "password" => $data['password']
            );

            if (!$token = $this->jwtauth->attempt($credentials)) {
                return response()->json(false, 401);
            }

            $user = $this->jwtauth->authenticate($token);
            if($user->user_role != 'admin')
            {
                return response()->json(false, 401);
            } else return response()->json(compact('token'));        

            
        }
    }

}
