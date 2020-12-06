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
            'nif' => \strtoupper($nif),
            'estat-civil' => $estatCivil,
            'sexe' => $sexe,
            'username' => $username,
            'password' => $contrasenya
        ];

        // S'intenta crear el fitxer json amb les dades de l'usuari. Si ja existeix peta
        if (!$this->crearFitxer($username, false, $json)) {
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

    // Carrega la pàgina que mostra les dades de l'usuari
    public function mostrarDadesUsuari() {
        // Comprova si hi ha alguna sessió iniciada, sino, la inicia
        if (\session_status() !== PHP_SESSION_ACTIVE) {
            \session_start();
        }

        // Si el login no es troba a true o no existeix, esborra les dades (per si de cas), i retorna la pàgina principal
        if (!isset($_SESSION['login']) || !$_SESSION['login']) {
            $this->logout();
            return $this->landingPage();
        }

        // Recupera totes les dades de la sessió i les envia al front
        $nom = $_SESSION['nom'];
        $cognoms = $_SESSION['cognoms'];
        $nif = $_SESSION['nif'];
        $estatCivil = $_SESSION['estat-civil'];
        $sexe = $_SESSION['sexe'];
        $username = $_SESSION['username'];

        return \view('userdata', ['nom' => $nom, 'cognoms' => $cognoms, 'nif' => $nif, 'estatCivil' => $estatCivil, 'sexe' => $sexe, 'username' => $username, 'errorDadesIncorrectes' => false, 'errorUsuariIncorrecte' => false, 'actualitzacioCorrecte' => false]);
    }

    // Permet actualitzar les dades de l'usuari
    public function actualitzarDades(Request $request) {
        // Comprova si hi ha alguna sessió iniciada, sino, la inicia
        if (\session_status() !== PHP_SESSION_ACTIVE) {
            \session_start();
        }

        // Si el login no es troba a true o no existeix, esborra les dades (per si de cas), i retorna la pàgina principal
        if (!isset($_SESSION['login']) || !$_SESSION['login']) {
            $this->logout();
            return $this->landingPage();
        }

        $nom = $request['name'];
        $cognoms = $request['surname'];
        $nif = $request['nif'];
        $estatCivil = $request['estat-civil'];
        $sexe = $request['sexe'];
        $username = $request['username'];
        $contrasenya = $request['password'];
        $contrasenya2 = $request['password2'];

        // Comprova si alguns dels paràmetres passats per la request està buit. Si està buit, retorna la pàgina de les dades, amb la informació emmagatzemada a la sessió i mostrant un error
        if (\strlen($nom) == 0 || \strlen($cognoms) == 0 || \strlen($nif) == 0 ||
            \strlen($estatCivil) == 0 || \strlen($sexe) == 0 || \strlen($username) == 0 ||
            \strlen($contrasenya) == 0 || \strlen($contrasenya2) == 0) {

                $nom = $_SESSION['nom'];
                $cognoms = $_SESSION['cognoms'];
                $nif = $_SESSION['nif'];
                $estatCivil = $_SESSION['estat-civil'];
                $sexe = $_SESSION['sexe'];
                $username = $_SESSION['username'];

                return \view('userdata', ['nom' => $nom, 'cognoms' => $cognoms, 'nif' => $nif, 'estatCivil' => $estatCivil, 'sexe' => $sexe, 'username' => $username, 'errorDadesIncorrectes' => true, 'errorUsuariIncorrecte' => false, 'actualitzacioCorrecte' => false]);
        }

        // Comprova si el DNI es correcte. Si no ho es retorna la pàgina amb les dades de la sessió i amb el missatge d'error
        if (!$this->nifCorrecte($nif)) {

            $nom = $_SESSION['nom'];
            $cognoms = $_SESSION['cognoms'];
            $nif = $_SESSION['nif'];
            $estatCivil = $_SESSION['estat-civil'];
            $sexe = $_SESSION['sexe'];
            $username = $_SESSION['username'];

            return \view('userdata', ['nom' => $nom, 'cognoms' => $cognoms, 'nif' => $nif, 'estatCivil' => $estatCivil, 'sexe' => $sexe, 'username' => $username, 'errorDadesIncorrectes' => true, 'errorUsuariIncorrecte' => false, 'actualitzacioCorrecte' => false]);
        }

        // Es fa el hash de les contrasenyes
        $contrasenya = \hash('sha256', $contrasenya);
        $contrasenya2 = \hash('sha256', $contrasenya2);

        // Comprova si les dues contrasenyes són iguals. Sino, retorna la pàgina amb les dades de la sessió i un missatge d'error
        if ($contrasenya !== $contrasenya2) {

            $nom = $_SESSION['nom'];
            $cognoms = $_SESSION['cognoms'];
            $nif = $_SESSION['nif'];
            $estatCivil = $_SESSION['estat-civil'];
            $sexe = $_SESSION['sexe'];
            $username = $_SESSION['username'];

            return \view('userdata', ['nom' => $nom, 'cognoms' => $cognoms, 'nif' => $nif, 'estatCivil' => $estatCivil, 'sexe' => $sexe, 'username' => $username, 'errorDadesIncorrectes' => true, 'errorUsuariIncorrecte' => false, 'actualitzacioCorrecte' => false]);
        }

        // Comprova si s'ha modificat el nom d'usuari. En cas positiu, s'haurà de canviar el nom del fitxer
        if ($username !== $_SESSION['username']) {

            $originalUsername = $_SESSION['username'];

            // S'intenta canviar el nom del fitxer. Si hi ha algun problema retorna la pàgina amb les dades de la sessió mostrant un missatge d'error
            if (!$this->canviarNomFitxer($originalUsername, $username)) {

                $nom = $_SESSION['nom'];
                $cognoms = $_SESSION['cognoms'];
                $nif = $_SESSION['nif'];
                $estatCivil = $_SESSION['estat-civil'];
                $sexe = $_SESSION['sexe'];
                $username = $_SESSION['username'];
    
                return \view('userdata', ['nom' => $nom, 'cognoms' => $cognoms, 'nif' => $nif, 'estatCivil' => $estatCivil, 'sexe' => $sexe, 'username' => $username, 'errorDadesIncorrectes' => true, 'errorUsuariIncorrecte' => true, 'actualitzacioCorrecte' => false]);    
            }
        }

        // Es prepara una variable amb les dades recollides en un diccionari
        $json = [
            'nom' => $nom,
            'cognoms' => $cognoms,
            'nif' => \strtoupper($nif),
            'estat-civil' => $estatCivil,
            'sexe' => $sexe,
            'username' => $username,
            'password' => $contrasenya
        ];
        
        // S'intenta crear el fitxer json amb les dades de l'usuari. Si ja existeix peta
        if (!$this->crearFitxer($username, true, $json)) {

            $nom = $_SESSION['nom'];
            $cognoms = $_SESSION['cognoms'];
            $nif = $_SESSION['nif'];
            $estatCivil = $_SESSION['estat-civil'];
            $sexe = $_SESSION['sexe'];
            $username = $_SESSION['username'];

            return \view('userdata', ['nom' => $nom, 'cognoms' => $cognoms, 'nif' => $nif, 'estatCivil' => $estatCivil, 'sexe' => $sexe, 'username' => $username, 'errorDadesIncorrectes' => true, 'errorUsuariIncorrecte' => true, 'actualitzacioCorrecte' => false]);    
        }

        // Es comprova que es poden carregar les dades del fitxer, i es carreguen a les variables de la sessió
        if (!$this->carregarDadesFitxer($username, $contrasenya)) {

            $nom = $_SESSION['nom'];
            $cognoms = $_SESSION['cognoms'];
            $nif = $_SESSION['nif'];
            $estatCivil = $_SESSION['estat-civil'];
            $sexe = $_SESSION['sexe'];
            $username = $_SESSION['username'];

            return \view('userdata', ['nom' => $nom, 'cognoms' => $cognoms, 'nif' => $nif, 'estatCivil' => $estatCivil, 'sexe' => $sexe, 'username' => $username, 'errorDadesIncorrectes' => true, 'errorUsuariIncorrecte' => true, 'actualitzacioCorrecte' => false]);    
        }

        // Comprova si hi ha alguna sessió iniciada, sino, la inicia
        if (\session_status() !== PHP_SESSION_ACTIVE) {
            \session_start();
        }

        // Si tot ha funcionat correctament, la variable de sessió 'login' es possa a true
        $_SESSION['login'] = true;

        // Es carreguen les dades de la sessió a les variables i es mostren en la pàgina amb les dades de l'usuari
        $nom = $_SESSION['nom'];
        $cognoms = $_SESSION['cognoms'];
        $nif = $_SESSION['nif'];
        $estatCivil = $_SESSION['estat-civil'];
        $sexe = $_SESSION['sexe'];
        $username = $_SESSION['username'];

        return \view('userdata', ['nom' => $nom, 'cognoms' => $cognoms, 'nif' => $nif, 'estatCivil' => $estatCivil, 'sexe' => $sexe, 'username' => $username, 'errorDadesIncorrectes' => false, 'errorUsuariIncorrecte' => false, 'actualitzacioCorrecte' => true]);    
    }

    public function eliminarUsuari() {
        
        // Comprova si hi ha alguna sessió iniciada, sino, la inicia
        if (\session_status() !== PHP_SESSION_ACTIVE) {
            \session_start();
        }

        // Comprova que la variable username existeix, per evitar que algú entri directament a aquesta url
        if (!isset($_SESSION['username'])) {
            return $this->logout;
        }

        // Prova a eliminar el fitxer. Si hi ha algun problema mostra la pantalla amb l'error
        if (!$this->eliminarFitxer($_SESSION['username'])) {
            return \view('userdata', ['nom' => $nom, 'cognoms' => $cognoms, 'nif' => $nif, 'estatCivil' => $estatCivil, 'sexe' => $sexe, 'username' => $username, 'errorDadesIncorrectes' => false, 'errorUsuariIncorrecte' => true, 'actualitzacioCorrecte' => false]);    
        }

        // Si tot ha anat bé, tanca la sessió
        return $this->logout();
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

    // S'encarrega de crear un fitxer amb les dades del json
    private function crearFitxer(String $username, bool $sobreescriure, array $json) {
        
        // Es comprova si es vol poder sobreescriure el fitxer o no
        if ($sobreescriure) {

            // S'intenta crear el fitxer json amb les dades de l'usuari. Si no existeix peta
            try {
                if (!Storage::disk('local')->exists("$username.json")) {
                    throw new \Exception("No existeix el fitxer");
                }
    
                Storage::put("$username.json", \json_encode($json));
            }
            catch (\Exception $e) {
                return false;
            }

            return true;
        }
        else {

            // S'intenta crear el fitxer json amb les dades de l'usuari. Si ja existeix peta
            try {
                if (Storage::disk('local')->exists("$username.json")) {
                    throw new \Exception("Ja existeix l'usuari");
                }

                Storage::put("$username.json", \json_encode($json));
            }
            catch (\Exception $e) {
                return false;
            }
            
            return true;
        }
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

    // Permet canviar el nom del fitxer. Retorna un boolean per saber si ho ha pogut fer o no
    private function canviarNomFitxer(String $nomOriginal, String $nomActualitzat) {

        // Comprova si el fitxer existeix
        try {
            if (!Storage::disk('local')->exists("$nomOriginal.json")) {
                throw new \Exception("No existeix el fitxer");
            }
        }
        catch (\Exception $e) {
            return false;
        }

        // Comprova que el fitxer que no existeix un fitxer amb el mateix nom amb el que volem actualitzar l'username
        try {
            if (Storage::disk('local')->exists("$nomActualitzat.json")) {
                throw new \Exception("Ja existeix el fitxer");
            }
        }
        catch (\Exception $e) {
            return false;
        }

        // Canvia el nom del fitxer
        try {
            Storage::move("$nomOriginal.json", "$nomActualitzat.json");
        }
        catch (\Exception $e) {
            return false;
        }

        return true;
    }

    // S'encarrega d'eliminar el JSON seleccionat
    private function eliminarFitxer(String $username) {

        // Comprova si el fitxer existeix i l'intenta eliminar
        try {
            if (!Storage::disk('local')->exists("$username.json")) {
                throw new \Exception("No existeix el fitxer");
            }

            Storage::delete("$username.json");
        }
        catch (\Exception $e) {
            return false;
        }

        return true;
    }
}
