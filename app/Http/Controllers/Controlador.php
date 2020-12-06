<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Controlador extends Controller
{
    
    // Controla si s'ha fet o no un login
    private $login;

    // Carrega la pàgina principal i li envia un boolean per saber si l'usuari està loginat o no
    public function landingPage() {

        // Comprova si hi ha alguna sessió iniciada, sino, la inicia
        if (\session_status() !== PHP_SESSION_ACTIVE) {
            \session_start();
        }

        if (isset($_SESSION['login'])) {
            $this->login = $_SESSION['login'];
        }
        else {
            $this->login = false;
            $_SESSION['login'] = $this->login;
        }

        $username = "";

        if ($this->login) {
            if (isset($_SESSION['username'])) {
                $username = $_SESSION['username'];
            }
        }

        return \view('landingpage', ['login' => $this->login, 'username' => $username]);
    }

    // Carrega la pàgina per fer login si l'usuari no està loginat. Si l'usuari ja està loginat envia a la Landing Page
    public function loginPage() {

        // Comprova si hi ha alguna sessió iniciada, sino, la inicia
        if (\session_status() !== PHP_SESSION_ACTIVE) {
            \session_start();
        }

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
            return \view('loginpage', ['errorDadesIncorrectes' => false, 'errorUsuariIncorrecte' => false]);
        }
    }

    // Permet fer login amb un usuari ja existent
    public function loginUser(Request $request) {
        $username = $request['username'];
        $password = $request['password'];

        // Comprova que els inputs tenen algun valor
        if (\strlen($username === 0) || \strlen($password) === 0) {
            return \view('loginpage', ['errorDadesIncorrectes' => true, 'errorUsuariIncorrecte' => true]);
        }

        // Es fa el hash de la password entrada pel formulari
        $password = \hash('sha256', $password);

        // Intenta carregar les dades del fitxer amb el nom de l'usuari
        if (!$this->carregarDadesFitxer($username, $password)) {
            return \view('loginpage', ['errorDadesIncorrectes' => false, 'errorUsuariIncorrecte' => true]);
        }

        // Comprova si hi ha alguna sessió iniciada, sino, la inicia
        if (\session_status() !== PHP_SESSION_ACTIVE) {
            \session_start();
        }
        
        // Es comparen les passwords. Si hi ha algun problema es fa logout i s'esborren totes les dades
        if ($password != $_SESSION['password']) {
            $this->logout();
            return \view('loginpage', ['errorDadesIncorrectes' => false, 'errorUsuariIncorrecte' => true]);
        }

        $_SESSION['login'] = true;

        // En cas de funcionar tot correctament s'inicia sessió i es retorna a la pàgina principal
        return $this->landingPage();
    }

    // Crea un nou usuari
    public function signUpUser(Request $request) {
        $nom = $request['name'];
        $cognoms = $request['surname'];
        $nif = $request['nif'];
        $estatCivil = $request['estat-civil'];
        $sexe = $request['sexe'];
        $username = $request['username'];
        $contrasenya = $request['password'];
        $contrasenya2 = $request['password2'];

        // Comprova si alguns dels paràmetres passats per la request està buit
        if (\strlen($nom) == 0 || \strlen($cognoms) == 0 || \strlen($nif) == 0 ||
            \strlen($estatCivil) == 0 || \strlen($sexe) == 0 || \strlen($username) == 0 ||
            \strlen($contrasenya) == 0 || \strlen($contrasenya2) == 0) {
                return \view('loginpage', ['errorDadesIncorrectes' => true, 'errorUsuariIncorrecte' => false]);
        }

        // Revisa que el DNI es correcte
        if (!$this->nifCorrecte($nif)) {
            return \view('loginpage', ['errorDadesIncorrectes' => false, 'errorUsuariIncorrecte' => true]);
        }

        // Es fa el hash de les contrasenyes
        $contrasenya = \hash('sha256', $contrasenya);
        $contrasenya2 = \hash('sha256', $contrasenya2);

        // Es comprova que les dues contrasenyes són iguals
        if ($contrasenya != $contrasenya2) {
            return \view('loginpage', ['errorDadesIncorrectes' => true, 'errorUsuariIncorrecte' => false]);
        }

        // Es prepara una variable amb les dades recollides en un diccionari
        $json = [
            'nom' => $nom,
            'cognoms' => $cognoms,
            'nif' => $nif,
            'estat-civil' => $estatCivil,
            'sexe' => $sexe,
            'username' => $username,
            'password' => $contrasenya
        ];

        // S'intenta crear el fitxer json amb les dades de l'usuari. Si ja existeix peta
        try {
            if (Storage::disk('local')->exists("$username.json")) {
                throw new \Exception("Ja existeix l'usuari");
            }

            Storage::put("$username.json", \json_encode($json));
        }
        catch (\Exception $e) {
            return \view('loginpage', ['errorDadesIncorrectes' => false, 'errorUsuariIncorrecte' => true]);
        }

        // Es comprova que es poden carregar les dades del fitxer, i es carreguen a les variables de la sessió
        if (!$this->carregarDadesFitxer($username, $contrasenya)) {
            return \view('loginpage', ['errorDadesIncorrectes' => true, 'errorUsuariIncorrecte' => false]);
        }

        // Comprova si hi ha alguna sessió iniciada, sino, la inicia
        if (\session_status() !== PHP_SESSION_ACTIVE) {
            \session_start();
        }

        // Si tot ha funcionat correctament, la variable de sessió 'login' es possa a true
        $_SESSION['login'] = true;

        return $this->landingPage();
    }

    // Tanca la sessió, eliminant totes les variables amb dades personals
    public function logout() {
        // Es comprova l'estat de la sessió, s'inicia una nova i s'esborren les variables emmagatzemades
        if (\session_status() !== PHP_SESSION_ACTIVE) {
            \session_start();
        }
        $this->login = false;
        $_SESSION['login'] = $this->login;
        $_SESSION['nom'] = null;
        $_SESSION['cognoms'] = null;
        $_SESSION['nif'] = null;
        $_SESSION['estat-civil'] = null;
        $_SESSION['sexe'] = null;
        $_SESSION['username'] = null;
        $_SESSION['password'] = null;

        return $this->landingPage();
    }

    // Comprova que el nif es correcte
    private function nifCorrecte(String $nif) {
        $correcte = false;
        $lletresNIF = "TRWAGMYFPDXBNJZSQVHLCKET";

        if (\strlen($nif) == 9) {
            $numerosNIF = substr($nif, 0, 8);
            $lletraNIF = substr(\strtoupper($nif), 8, 9);

            if (is_numeric($numerosNIF)) {
                $numerosNIF = \intval($numerosNIF);
                $posicioLletra = $numerosNIF % 23;
                $lletraValida = substr($lletresNIF, $posicioLletra);

                $correcte = true;
                if ($lletraValida == $lletraNIF) {
                    $correcte = true;
                }
            }
        }

        return $correcte;
    }

    private function carregarDadesFitxer(String $username, String $password) {
        $fitxer;

        try {
            if (Storage::disk('local')->missing("$username.json")) {
                throw new \Exception("No existeix el fitxer");
            }

            $fitxer = Storage::get("$username.json");
        }
        catch (\Exception $e) {
            return false;
        }

        // Es passen les dades del fitxer a format diccionari
        $json = \json_decode($fitxer, true);

        // Es comprova que tots els paràmetres necessaris es troben en el diccionari
        if (!(isset($json['nom']) && isset($json['cognoms']) && isset($json['nif']) &&
            isset($json['estat-civil']) && isset($json['sexe']) && isset($json['username']) &&
            isset($json['password']))) {
                return false;
        }

        if ($password != $json['password']) {
            return false;
        }

        // Es comprova l'estat de la sessió, s'inicia una nova i es guarden les variables
        if (\session_status() !== PHP_SESSION_ACTIVE) {
            \session_start();
        }

        $_SESSION['nom'] = $json['nom'];
        $_SESSION['cognoms'] = $json['cognoms'];
        $_SESSION['nif'] = $json['nif'];
        $_SESSION['estat-civil'] = $json['estat-civil'];
        $_SESSION['sexe'] = $json['sexe'];
        $_SESSION['username'] = $json['username'];
        $_SESSION['password'] = $json['password'];

        return true;
    }
}
