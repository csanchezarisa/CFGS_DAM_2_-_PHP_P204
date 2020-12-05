<!doctype html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="icon" type="image/png" href="logo.png">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css" integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.0/css/all.css" integrity="sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ" crossorigin="anonymous">
        <title>@yield('title')</title>
    </head>
    <body>
        <!-- Barra de navegació -->
        <nav class="navbar navbar-expand-sm bg-dark navbar-dark fixed-top">
            <a class="navbar-brand" href="/">
                <img src="logo.png" alt="Logo" style="width:40px;"></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#collapsibleNavbar">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="collapsibleNavbar">
                <ul class="navbar-nav">

                    <li class="nav-item @yield('activeInici')">
                        <a class="nav-link" href="/">Inici</a>
                    </li>

                    <li class="nav-item @yield('activeLogin')" style="@yield('login')">
                        <a class="nav-link" href="#">Iniciar sessió</a>
                    </li>

                    <li class="nav-item @yield('activePersonalData')" style="@yield('logout')">
                        <a class="nav-link" href="#">Dades personals</a>
                    </li>

                    <li class="nav-item" style="@yield('logout')">
                        <a class="nav-link" href="#">Tancar sessió</a>
                    </li>

                </ul>
            </div>
        </nav>

        <!-- Contingut principal de la web -->
        <div class="container" style="margin-top: 75px; margin-bottom: 75px;">
            @yield('content')
        </div>

        <!-- Peu de pàgina -->
        <footer class="footer bg-dark" style="position: fixed; left: 0; bottom: 0; padding: 10px; width: 100%;">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <span class="text-muted">
                            By: Cristóbal Sánchez Arisa
                        </span>
                    </div>
                    <div class="col-sm-6" style="text-align: right;">
                        <button type="button" class="btn btn-dark" style="@yield('footerInici')" data-toggle="tooltip" data-placement="top" title="Inici" onclick="window.location.href='/'">
                            <i class="fas fa-home"></i>
                        </button>
                        <button type="button" class="btn btn-dark" style="@yield('login')" data-toggle="tooltip" data-placement="top" title="Iniciar sessió" onclick="window.location.href='/login'">
                            <i class="fas fa-sign-in-alt"></i>
                        </button>
                        <button type="button" class="btn btn-dark" style="@yield('logout')" data-toggle="tooltip" data-placement="top" title="Tancar sessió" onclick="window.location.href='/logout'">
                            <i class="fas fa-sign-out-alt"></i>
                        </button>
                    </div>
                </div>
            </div>
        </footer>

        <!-- Scripts de bootstrap -->
        <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ho+j7jyWK8fNQe+A12Hb8AhRq26LrZ/JpcUGGOn+Y7RsweNrtN/tE3MoK7ZeZDyx" crossorigin="anonymous"></script>
        
        <!-- Scripts que obren els tooltips -->
        <script>
            $(document).ready(function(){
              $('[data-toggle="tooltip"]').tooltip();
            });
        </script>
    </body>
</html>