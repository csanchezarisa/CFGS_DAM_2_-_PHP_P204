<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Controlador extends Controller
{
    
    // Controla si s'ha fet o no un login
    private $login;

    // Carrega la pàgina principal i li envia un boolean per saber si l'usuari està loginat o no
    public function landingPage() {
        session_start();
        if (isset($_SESSION['login'])) {
            $this->login = $_SESSION['login'];
        }
        else {
            $this->login = false;
            $_SESSION['login'] = $this->login;
        }

        return \view('landingpage', ['login' => $this->login]);
    }

    // Carrega la pàgina per fer login si l'usuari no està loginat. Si l'usuari ja està loginat envia a la Landing Page
    public function loginPage() {
        session_start();
        if (isset($_SESSION['login'])) {
            $this->login = $_SESSION['login'];
        }
        else {
            $this->login = false;
            $_SESSION['login'] = $this->login;
        }
        
        if ($this->login) {
            return \view('landingpage', ['login' => $this->login]);
        }
        else {
            return \view('loginpage', ['errorDadesIncorrectes' => true, 'errorUsuariIncorrecte' => true]);
        }
    }

    public function login() {
        $this->login = true;

        return \view('landingpage', ['login' => $this->login]);
    }

    public function logout() {
        $this->login = false;

        return \view('landingpage', ['login' => $this->login]);
    }

}
