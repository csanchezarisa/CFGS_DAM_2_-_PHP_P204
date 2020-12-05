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

@else

    <!-- Amaga els botons que permeten tancar sessió -->
    @section('logout')
        display: none;
    @endsection

@endif