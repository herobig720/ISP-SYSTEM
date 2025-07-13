<?php

namespace Modules\Client\Http\Controllers;

use Illuminate\Routing\Controller;

class ClientController extends Controller
{
    public function index()
    {
        return response()->json(['message' => 'Client module controller working!']);
    }
}