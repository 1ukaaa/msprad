<?php

class ModeleBrutForce {
    static private $bdd;

    function __construct() {

    }

    public function checkBrutForce($mail) {
        $dataBrutForceClient = $this->getDataBrutForceClientRequest($mail);
        $brutForceReturn['isStillBlocked'] = FALSE;
        if ($dataBrutForceClient['blocked']) {
            $brutForceReturn = $this->checkIfAbleToUnblock($dataBrutForceClient);
        } else {
            $this->checkIfAbleToBlock($dataBrutForceClient);
        }
        $dataBrutForceClient = $this->getDataBrutForceClientRequest($mail);
        $this->addRequest($dataBrutForceClient);
        return $brutForceReturn;
    }

    private function checkIfAbleToUnblock($dataBrutForceClient){
        $brutForceReturn = array();
        $lastRequest = strtotime($dataBrutForceClient['lastRequest']);
        $leftTime = strtotime("now") - $lastRequest;
        $isStillBlocked = TRUE;
        if ($leftTime > BLOCK_TIME){
            $isStillBlocked = FALSE;
            $this->resetBrutForce($dataBrutForceClient['idBrutForce']);
        }
        $brutForceReturn['isStillBlocked'] = $isStillBlocked;
        $brutForceReturn['leftTime'] = BLOCK_TIME - $leftTime;
        if($brutForceReturn['leftTime'] == 0){
            $brutForceReturn['leftTime'] = 1;
        }
        return $brutForceReturn;
    }
    
    private function checkIfAbleToBlock($dataBrutForceClient){
        $lastRequest = strtotime($dataBrutForceClient['lastRequest']);
        $firstRequest = strtotime($dataBrutForceClient['firstRequest']);
        $intervalRequestTime = $lastRequest - $firstRequest;
        if ($intervalRequestTime < INTERVAL_REQUEST_TIME && $dataBrutForceClient['numberRequest'] >= INTERVAL_REQUEST_NUMBER) {
           $this->enableBrutForce($dataBrutForceClient);
        }else if ($dataBrutForceClient['numberRequest'] >= INTERVAL_REQUEST_NUMBER && $intervalRequestTime > INTERVAL_REQUEST_TIME) {
            $this->resetBrutForce($dataBrutForceClient['idBrutForce']);
        }
    }

    // REQUEST MODELE BRUT FORCE

    private function getDataBrutForceClientRequest($mail) {
        self::$bdd=ModeleBdd::ConnexionBDD();
        try{
            $requete = self::$bdd->prepare("
            SELECT `idBrutForce`, `firstRequest`, `lastRequest`, `numberRequest`, `blocked` 
            FROM brutforce
            INNER JOIN users ON brutforce.idUser = users.idUser
            and users.mailUser = :mail");
            $requete->bindParam('mail', $mail);
            $requete->execute();
            $requete = $requete->fetch();
        }catch(Exception $e){
            die('Veuillez faire '.$e->getMessage());
        }
        return $requete;
    }

    private function resetBrutForce($idBrutForce) {
        self::$bdd=ModeleBdd::ConnexionBDD();
        try{
            $now = new DateTime();
            $now = $now->format('Y-m-d H:i:s');
            $requete = self::$bdd->prepare("
            UPDATE brutforce SET `firstRequest` = :firstRequest, `lastRequest` = :lastRequest, `numberRequest` = 0, `blocked` = 0 
            WHERE idBrutForce = :idBrutForce");
            $requete->bindParam('firstRequest', $now);
            $requete->bindParam('lastRequest', $now);
            $requete->bindParam('idBrutForce', $idBrutForce);
            $requete->execute();
            $requete = $requete->fetch();
        }catch(Exception $e){
            die('Veuillez faire '.$e->getMessage());
        }
        return $requete;
    }

    private function enableBrutForce($dataBrutForceClient){
        self::$bdd=ModeleBdd::ConnexionBDD();
        try{
            $now = new DateTime();
            $now = $now->format('Y-m-d H:i:s');
            $requete = self::$bdd->prepare("
            UPDATE brutforce SET `blocked` = 1
            WHERE idBrutForce = :idBrutForce");
            $requete->bindParam('idBrutForce', $dataBrutForceClient['idBrutForce']);
            $requete->execute();
            $requete = $requete->fetch();
        }catch(Exception $e){
            die('Veuillez faire '.$e->getMessage());
        }
        return $requete;
    }
    
    private function addRequest($dataBrutForceClient){
        self::$bdd=ModeleBdd::ConnexionBDD();
        try{
            $now = new DateTime();
            $now = $now->format('Y-m-d H:i:s');
            $numberRequest = $dataBrutForceClient['numberRequest'] + 1;
            $requete = self::$bdd->prepare("
            UPDATE brutforce SET `lastRequest` = :lastRequest, `numberRequest`= :numberRequest
            WHERE idBrutForce = :idBrutForce");
            $requete->bindParam('lastRequest', $now);
            $requete->bindParam('numberRequest', $numberRequest);
            $requete->bindParam('idBrutForce', $dataBrutForceClient['idBrutForce']);
            $requete->execute();
            $requete = $requete->fetch();
        }catch(Exception $e){
            die('Veuillez faire '.$e->getMessage());
        }
        return $requete;
    }
}