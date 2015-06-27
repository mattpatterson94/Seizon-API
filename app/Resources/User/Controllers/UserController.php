<?php namespace App\Resources\User\Controllers;

use App\Http\Controllers\CRUDController;
use App\Resources\User\Services\UserService;

class UserController extends CRUDController
{
    public function __construct()
    {
        parent::__construct(new UserService());
    }
}
