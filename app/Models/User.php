<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $fillable = ['email','password','role','supplier_id','pharmacy_id','admin_id'];
    protected $hidden = ['password'];
}
