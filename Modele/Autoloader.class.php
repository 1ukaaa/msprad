<?php
/**
 * Class d'autolaoder de fichier
 */
class Autoloader
{
  public static function register()
  {
    spl_autoload_register([__CLASS__, 'autoload']);
  }
  public static function autoload($class)
  {
    $lien = 'Modele/' . $class . '.class.php';
    if (!is_file($lien)) {
      $lien = 'Controleur/' . $class . '.class.php';
    }
    if (is_file($lien)) {
      require_once $lien;
    }
  }
}