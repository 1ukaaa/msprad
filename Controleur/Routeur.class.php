<?php
    class Routeur
    {
        public function routeurRequete(){
            try {
                $route = $_GET['route'] ?? null;
                $action = $_GET['action'] ?? null;
                if (($route || $action) == null) {
                    throw new Exception('Erreur route');
                }
                $instance = new $route();
                $instance->$action();
            } catch (Exception $e) {
                throw new Exception('Erreur : '.$e);
            }
        }
    }
?>