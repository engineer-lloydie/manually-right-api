<?php

namespace App\Contracts;

use Illuminate\Http\Request;

interface AuthLoginInterface
{
    public function login(Request $request);
}