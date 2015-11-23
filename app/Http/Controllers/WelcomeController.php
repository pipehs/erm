<?php

namespace Ermtool\Http\Controllers;

use Illuminate\Http\Request;
use Ermtool\Http\Requests;
use Ermtool\Http\Controllers\Controller;

class WelcomeController extends Controller
{
    public function index()
    {
        return \View::make('welcome');   
    }
}
