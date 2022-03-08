<?php
    require 'Modele/Autoloader.class.php';
    require 'Modele/constante.php';
    session_start();
    Autoloader::register();
    Log::initialize();
    new GestionException;

    $routeur = new Routeur();
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        $routeur = new RouteurAjax();
      }
    $routeur->routeurRequete();
?>