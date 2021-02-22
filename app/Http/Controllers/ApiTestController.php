<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class ApiTestController extends BaseController
{
    public function index()
    {
        return view('apitest/index');
    }

}

