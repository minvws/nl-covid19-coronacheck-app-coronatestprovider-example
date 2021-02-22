<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;

class Controller extends BaseController
{
    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('index');
    }
}
