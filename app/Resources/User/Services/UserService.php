<?php namespace App\Resources\User\Services;

use App\Repository;
use App\Resources\User\Models\User as User;

class UserService extends Service {

    protected $name = "User";

    protected $rules = [
        'email' => 'required|email|max:255|unique:users',
        'password' => 'required|confirmed|min:6',
    ];

    function __construct()
    {
        parent::__construct(new User);
    }

    public function validator(array $data)
    {
        $this->rules['email'] = 'required|email|max:255|unique:users' . ((isset($data['id']))?",email, " . $data['id']:"");
        return parent::validator($data);
    }

    public function create(array $data)
    {
        $isChef = (isset($data['user_role']) && $data['user_role'] == 'chef');
        $validation = $this->validator($data);
        if($validation->fails())
        {
            return $this->responseService->validationError($validation->errors()->all());
        }
        else
        {
            try {
                $obj = $this->model->create($data);
                if($isChef)
                {
                    $data['chef']['user_id'] = $obj->id;
                    return $chefObj = $this->chefService->create($data['chef']);
                }
            } catch (Exception $e) {
                return $this->responseService->badRequest($e->getMessage());
            }
            return $this->responseService->ok($this->name . ' created successfully', $obj);
        }
    }

    public function get($id)
    {
        try {
            $obj = $this->model->findOrFail($id);
            if($obj->user_role == 'chef')
            {
                $obj['chef'] = $obj->chef;
                return $this->responseService->data($this->transformer->transformChef($obj->toArray()));
            }
            return $this->responseService->data($this->transformer->transform($obj->toArray()));
        }
        catch(\Exception $e)
        {
            return $this->responseService->notFound($this->name . ' with this ID does not exist');
        }
    }

    public function update($id, array $data)
    {
        if(!isset($data['password'])) 
        {
            $this->rules['password'] = '';
        }
        $obj = $this->model->find($id);

        $validation = $this->validator( (array) $data);
        if($validation->fails())
        {
            return $this->responseService->validationError($validation->errors()->all());
        }
        else
        {
            try {
                $obj->update($data);
                if($obj->user_role == 'chef')
                {
                    $this->chefService->update($obj->chef->id, $data['chef']);
                }
            } catch (Exception $e) {
                return $this->responseService->badRequest($e->getMessage());
            }
        
            return $this->responseService->ok($this->name . ' updated successfully');
        }
    }

} 
