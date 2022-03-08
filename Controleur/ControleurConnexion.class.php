<?php
class ControleurConnexion
{
    function __construct() {
        $this->modeleConnexion = new ModeleConnexion();
        $this->modeleBrutForce = new ModeleBrutForce();
    }

    public function connexion(){
        $vue = new GenerateVue("Connexion", array());
        $vue->generer();
    }

    public function makeConnexion(){
        $mail = $_POST['mailUser'];
        $password = $_POST['passwordUser'];
        if (empty($mail) || empty($password)) {
            throw new Exception("Data Empty");
        }
         
        $dataReturn = [
            'goodPassword' => false,
            'googleAuthenticator' => [
                'firstConnexion' => false,
                'imgUrl' => false,
            ],
            'brutForce' => false,
        ];
            $authorizedConnexion = $this->modeleConnexion->checkIfUsersExistsInActiveDirectory($mail, $password);
            $dataReturn['goodPassword'] = $authorizedConnexion;
            if ($authorizedConnexion) {
                $qrCode = $this->modeleConnexion->checkIfUsersExistsInBdd($mail);
                $dataReturn['googleAuthenticator']['imgUrl'] = $qrCode;
                if ($qrCode) {
                    $dataReturn['googleAuthenticator']['firstConnexion'] = true;
                }
                $dataReturn['brutForce'] = $this->manageBrutForce($mail);
                $this->modeleConnexion->checkBrowser($mail);
                $this->modeleConnexion->checkIp($mail);
            }
        GestionAjax::$retourAjax['messageReussite'] = "success";
        GestionAjax::$retourAjax['data'] = $dataReturn;
    }

    public function connectAfterFirstConnexion(){

        $code = $_POST['googleAuthentificatorCode'];
        $mailUser = $_POST['mailUser'];
        $checkIfAccountBlocked = $this->modeleConnexion->checkIfAccountBlocked($mailUser);
        $connected = false;
        if (!$checkIfAccountBlocked) {
            $connected = $this->modeleConnexion->checkIfCodeGoogleAuthenticationIsCorrect($code, $mailUser);
        }
        GestionAjax::$retourAjax['messageReussite'] = "success";
        GestionAjax::$retourAjax['data'] = $connected;
    }

    public function unlockAccount() {
        $secret = $_GET['secret'];
        $newBrowser = $_GET['newBrowser'];
        $mail = $_GET['mail'];
        if (empty($secret) || empty($newBrowser) || empty($mail)) {
            throw new Exception("Data Empty");
        }
        $this->modeleConnexion->unlockBrowserAccount($secret, $newBrowser, $mail);

    }

    private function manageBrutForce($mail) {
        return $this->modeleBrutForce->checkBrutForce($mail);
        
    }
}