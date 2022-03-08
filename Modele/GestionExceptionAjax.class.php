<?php

class GestionExceptionAjax extends GestionException
{
  public function __construct()
  {
    parent::__construct();
  }
  /**
   * Fonction qui est appellée quand un execption est levée
   * @param [Exception] $exception
   * @return void
   */
  public function attraperException($exception)
  {
    parent::attraperException($exception);
    if (is_object($exception) && GestionAjax::$codeRetourAjax == 200) {
      GestionAjax::$codeRetourAjax = 520;
    }
    $retourAjax = GestionAjax::$retourAjax;
    if ($retourAjax["messageErreur"] == "") {
      $retourAjax["messageErreur"] ="Une erreur est survenue veuillez reessayer";
    }
    $retourAjax["exception"] = $this->dataException;
    GestionAjax::$retourAjax = $retourAjax;
    GestionAjax::sendResponseAjax();
  }
}