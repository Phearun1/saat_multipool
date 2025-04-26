<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'users';
    protected $primaryKey = 'user_id';
    protected $fillable = ['username', 'password_hash', 'phone_number', 'email', 'full_name', 'user_type'];
}
