<?php namespace App\Resources\User\Models;

use Illuminate\Database\Eloquent\Model;

class UserRole extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'user_roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['role'];

    public $timestamps = false;

}