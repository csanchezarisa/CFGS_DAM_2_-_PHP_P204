<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Controlador extends Controller
{
    
    private $login = false;

    public function landingPage() {
        return \view('landingpage', ['login' => $this->login]);
    }

}
