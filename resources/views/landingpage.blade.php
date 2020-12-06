@extends('layout.mainmaster')

@section('title')
    Inici
@endsection

@section('activeInici')
    active
@endsection


<!-- Revisa si s'ha fet login per mostrar unes opcions o unes altres -->
@if ($login)
    
    <!-- Amaga els botons que permeten fer login -->
    @section('login')
        display: none;
    @endsection

    @section('content')
<h1 style="text-align: center;">{{$username}} has fet login!</h1>
        <h2 style="text-align: center;">Així m'agrada, gràcies a aquesta acció, un ximpancé ha sigut apadrinat</h2>
        <div class="container" style="text-align: center;">
            <img src="https://media1.tenor.com/images/03cb574d782243be49c07e5432eda668/tenor.gif?itemid=10879973" alt="Dancing chimpanzee">
        </div>
    @endsection

@else

    <!-- Amaga els botons que permeten tancar sessió -->
    @section('logout')
        display: none;
    @endsection

    @section('content')
        <h1 style="text-align: center;">Fes login per poder utilitzar aquesta aplicació web!</h1>
        <div class="container" style="text-align: center;">
            <img src="https://media1.tenor.com/images/aa574640b0f3e2c22a4798233212e35d/tenor.gif?itemid=13052487" alt="Waiting">
        </div>
    @endsection

@endif