<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    protected $table = 'admins';
    protected $fillable = ['first_name', 'last_name', 'phone_number', 'password', 'super_admin', 'token'];
}
