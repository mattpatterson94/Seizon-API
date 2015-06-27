<?php namespace App\Resources\User\Repositories;

use App\Repository;
use App\Resources\User\Models\User as User;

class UserRepository extends Repository
{
    protected $name = "User";

    protected $rules = [
        'email' => 'required|email|max:255|unique:users',
        'password' => 'required|confirmed|min:6',
    ];

    public function __construct()
    {
        parent::__construct(new User);
    }

    public function validator(array $data)
    {
        $this->rules['email'] = 'required|email|max:255|unique:users'
            . ((isset($data['id'])) ? ",email, " . $data['id'] : "");

        return parent::validator($data);
    }

    public function update($id, $data)
    {
        if (!isset($data['password'])) {
            $this->rules['password'] = '';
        }

        return parent::update($id, $data);
    }
}
