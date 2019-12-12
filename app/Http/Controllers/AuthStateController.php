<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuthStateController extends Controller
{
    public function checkAuth ()
    {
        if (request()->ajax()) {
           return ['auth' => request()->session()->get('auth')];
        }
    }
}
