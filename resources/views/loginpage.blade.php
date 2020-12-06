@extends('layout.mainmaster')

@section('title')
    Iniciar sessió
@endsection

<!-- Marca la pàgina d'iniciar sessió en la barra de navegació -->
@section('activeLogin')
    active
@endsection

<!-- Amaga els botons que permeten fer login -->
@section('logout')
    display: none;
@endsection

@section('content')

    <!-- Si el back troba un error, mostrarà aquesta pàgina activant l'apartat següent amb una alerta-->
    @if ($errorDadesIncorrectes)
        <div class="row">
            <div class="col-sm-12" style="text-align: center;">
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>Error!</strong> Les dades introduides no son correctes
                </div>
            </div>
        </div>
    @endif

    @if ($errorUsuariIncorrecte)
        <div class="row">
            <div class="col-sm-12" style="text-align: center;">
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>Error!</strong> Usuari incorrecte
                </div>
            </div>
        </div>
    @endif

    <!-- Formulari -->
    <div class="row" style="padding-top: 25px; padding-bottom: 25px;">
        <div class="col-sm-6" style="text-align: center;">
            <h2>Iniciar sessió</h2>

            <form action="/login-user" method="post" style="text-align: left;" id="login-form" onchange="activarFormulari('login-form')">
                @csrf
                <div class="form-group">
                    <label for="username">Nom d'usuari:</label>
                    <input type="username" class="form-control" placeholder="Nom d'usuari" name="username" id="username-login" required>
                    <div class="invalid-feedback">Introdueix un nom d'usuari</div>
                </div>
                <div class="form-group">
                    <label for="password">Contrasenya:</label>
                    <input type="password" class="form-control" placeholder="Contrasenya" name="password" id="password-login" required>
                    <div class="invalid-feedback">Introdueix una contrasenya</div>
                </div>
                <div style="text-align: right;">
                    <button type="submit" class="btn btn-dark" id="btn-login" disabled>
                        <i class="fas fa-sign-in-alt"></i>
                    </button>
                </div>
            </form>
        </div>
        <div class="col-sm-6" style="text-align: center;">
            <h2>Registrar-te</h2>

            <form action="/signup-user" method="post" style="text-align: left;" id="signup-form" onchange="activarFormulari('signup-form')">
                @csrf
                <div class="form-group">
                    <label for="name">Nom:</label>
                    <input type="text" class="form-control" placeholder="Nom" name="name" id="name-signup" required>
                    <div class="invalid-feedback">Introdueix un nom</div>
                </div>
                <div class="form-group">
                    <label for="surname">Cognoms:</label>
                    <input type="text" class="form-control" placeholder="Cognoms" name="surname" id="surname-signup" required>
                    <div class="invalid-feedback">Introdueix els cognoms</div>
                </div>
                <div class="form-group">
                    <label for="nif">NIF:</label>
                    <input type="text" class="form-control" placeholder="NIF" name="nif" id="nif-signup" required>
                    <div class="invalid-feedback" id="invalid-nif">Introdueix un NIF</div>
                </div>
                <div class="form-group">
                    <label for="estat-civil">Estat civil:</label>
                    <select class="form-control" name="estat-civil" id="estat-civil-signup">
                        <option>Solter/a</option>
                        <option>Casat/ada</option>
                        <option value="Unió_lliure">Unió lliure o de fet</option>
                        <option>Separat/ada</option>
                        <option>Divorciat/ada</option>
                        <option>Vidu/a</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="sexe">Sexe:</label>
                    <select class="form-control" name="sexe" id="sexe-signup">
                        <option>Maculí</option>
                        <option>Femení</option>
                        <option>Altres</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="username">Nom d'usuari:</label>
                    <input type="username" class="form-control" placeholder="Nom d'usuari" name="username" id="username-signup" required>
                    <div class="invalid-feedback">Introdueix un nom d'usuari</div>
                </div>
                <div class="form-group">
                    <label for="password">Contrasenya:</label>
                    <input type="password" class="form-control" placeholder="Contrasenya" name="password" id="password-signup" required>
                    <div class="invalid-feedback">Introdueix una contrasenya</div>
                </div>
                <div class="form-group">
                    <label for="password2">Repetir contrasenya:</label>
                    <input type="password" class="form-control" placeholder="Repetir contrasenya" name="password2" id="password2-signup" required>
                    <div class="invalid-feedback" id="invalid-password2">Introdueix una contrasenya</div>
                </div>
                <div style="text-align: right;">
                    <button type="submit" class="btn btn-dark" id="btn-signup" disabled>
                        <i class="fas fa-user-plus"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Activa els camps Required del formulari seleccionat
        function activarFormulari(idFormulari) {
            var formulari = document.getElementById(idFormulari);
            formulari.classList.add("was-validated");

            if (idFormulari == "login-form") {
                revisarLoginForm();
            }
            else if (idFormulari == "signup-form") {
                revisarSignUpForm();
            }
        }

        // Revisa els camps del formulari de login
        function revisarLoginForm() {
            var username = document.getElementById("username-login").value;
            var password = document.getElementById("password-login").value;
            var btnSubmit = document.getElementById("btn-login");

            if (username.length > 0 && password.length > 0) {
                btnSubmit.removeAttribute("disabled");
            }
            else {
                btnSubmit.setAttribute("disabled", "true");
            }
        }

        // Revisa els camps del formulari de registre
        function revisarSignUpForm() {
            var nom = document.getElementById("name-signup").value;
            var cognoms = document.getElementById("surname-signup").value;
            var nif = document.getElementById("nif-signup");
            var invalidNifMessage = document.getElementById("invalid-nif");
            var nomUsuari = document.getElementById("username-signup").value;
            var contrasenya = document.getElementById("password-signup");
            var contrasenya2 = document.getElementById("password2-signup");
            var invalidContrasenya2 = document.getElementById("invalid-password2");
            var btnSubmit = document.getElementById("btn-signup");

            if(nif.value.length == 0) {
                invalidNifMessage.innerHTML = "Introdueix un nif."
            }
            else {
                if (!nifCorrecte(nif.value)) {
                    invalidNifMessage.innerHTML = "Introdueix un nif correcte."
                    nif.value = null;
                }
            }

            if (contrasenya.value != contrasenya2.value) {
                invalidContrasenya2.innerHTML = "Les contrasenyes no coincideixen.";
                contrasenya2.value = null;
            }

            if (nom.length > 0 && cognoms.length > 0 && nif.value.length > 0 && nomUsuari.length > 0 && contrasenya.value.length > 0 && contrasenya2.value.length > 0) {
                btnSubmit.removeAttribute("disabled");
            }
            else {
                btnSubmit.setAttribute("disabled", "true");
            }
        }

        // Revisa si el NIF que se li passa és correcte
        function nifCorrecte(nif) {
            var correcte = false;
            const lletresNIF = "TRWAGMYFPDXBNJZSQVHLCKET";

            if (nif.length == 9) {
                var numerosNIF = nif.substring(0, 8);
                var lletraNIF = nif.toUpperCase().charAt(8);

                // Revisa si els 8 primers caràcters són números
                if (/^\d+$/.test(numerosNIF)) {
                    numerosNIF = parseInt(numerosNIF);
                    var posicioLletra = numerosNIF % 23;
                    var lletraValida = lletresNIF.charAt(posicioLletra)
                    
                    // Si la lletra vàlida es igual que lletra introduida en el DNI la comprovació serà correcte
                    if (lletraValida == lletraNIF) {
                        correcte = true;
                    }
                };
            };


            return correcte;
        }
    </script>
@endsection