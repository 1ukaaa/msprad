<?php

class RouteurAjax
{
  public function routeurRequete()
  {
    Log::addLog("Début chargement controleur ajax");
    new GestionExceptionAjax();
    GestionAjax::$retourAjax = array("messageErreur" => "");
    GestionAjax::$codeRetourAjax = 200;
    GestionAjax::$optionJSONEncode = JSON_THROW_ON_ERROR;
    $routeur = new Routeur();
    $routeur->routeurRequete();
    require_once("Modele/sendReponse.php");
    Log::addLog("Ok fin controleur ajax");
  }
}