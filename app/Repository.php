<?php namespace App;

use Validator;
use App\Services\ResponseService;
use App;

abstract class Repository
{
    // Name of Resource
    protected $name;
    // Validation rules
    protected $rules;
    // Resource model
    protected $model;
    // Response service
    protected $responseService;

    protected $validator;

    public function __construct($model)
    {
        $this->model = $model;
        $this->responseService = new ResponseService();
        $this->validator = new Validator();
    }

    public function validator(array $data)
    {
        return App::make('validator')->make($data, $this->rules);
    }

    public function create($data)
    {
        $validation = $this->validator($data);
        if ($validation->fails()) {
            return $this->responseService->validationError($validation->errors()->all());
        }
        try {
            $this->model->create($data);
        } catch (Exception $e) {
            return $this->responseService->badRequest($e->getMessage());
        }
        return $this->responseService->ok($this->name . ' created successfully');
    }

    public function all()
    {
        $obj = $this->model->all();
        return $this->responseService->data($obj->toArray());
    }

    public function get($id)
    {
        try {
            $obj = $this->model->findOrFail($id);
            return $this->responseService->data($obj->toArray());
        } catch (\Exception $e) {
            return $this->responseService->notFound($this->name . ' with this ID does not exist');
        }
    }

    public function update($id, $data)
    {
        $obj = $this->model->find($id);

        $validation = $this->validator($data);
        if ($validation->fails()) {
            return $this->responseService->validationError($validation->errors()->all());
        }
        try {
            $obj->update($data);
        } catch (Exception $e) {
            return $this->responseService->badRequest($e->getMessage());
        }

        return $this->responseService->ok($this->name . ' updated successfully');
    }

    public function destroy($id)
    {
        $obj = $this->model->find($id);

        try {
            $obj->delete();
        } catch (Exception $e) {
            return $this->responseService->badRequest($e->getMessage());
        }

        return $this->responseService->ok($this->name . ' deleted successfully');
    }

    public function search($query)
    {
        $obj = $this->model;

        foreach ($query as $key => $attribute) {
            $pairs = explode(',', $attribute);

            $obj = $obj->where(function ($innerQuery) use ($pairs, $key) {
                foreach ($pairs as $string) {
                    $innerQuery->orWhere($key, 'LIKE', '%'.$string.'%');
                }
            });
        }

        $obj = $obj->get();

        return $this->responseService->data($this->transformer->transformObject($obj));
    }
}
