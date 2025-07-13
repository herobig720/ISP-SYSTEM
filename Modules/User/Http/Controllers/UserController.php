<?php

namespace Modules\User\Http\Controllers;

use Illuminate\Routing\Controller;

class UserController extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'User module controller working!']);
    }
}