<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Session;

class RoleMiddleware
{
    public function handle($role)
    {
        if(!Session::has('role') || Session::get('role') != $role) {
            die('Unauthorized Access');
        }
    }
}
