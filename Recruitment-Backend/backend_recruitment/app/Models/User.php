<?php

namespace App\Models;

//use Jenssegers\Mongodb\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Authenticatable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
// use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Model as Eloquent;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Eloquent implements AuthenticatableContract, AuthorizableContract, CanResetPasswordContract, JWTSubject
{
	use Authenticatable, Authorizable, CanResetPassword, Notifiable;

	public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    protected $fillable = ['name','phone','gender','email','Password'];   

    public function getJWTCustomClaims()
    {
        return [];
    }
}
