<?php 
class ModeleBdd
{
static private $host=HOST_BDD;
static private $login=LOGIN_BDD;
static private $mdp=MDP_BDD;
static private $db='connexion';
static private $port=3306;
static private $encode=array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');

public static function ConnexionBDD()
{
  try
  {
    $bdd = new PDO('mysql:host='.self::$host.";port=".self::$port.";".'dbname='.self::$db, self::$login,self::$mdp,self::$encode);//acces BDD
  }
  catch (Exception $e)
  {
    die('Erreur : ' . $e->getMessage());
  }
  return $bdd;
}
}
?>