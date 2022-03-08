<?php
include_once 'vendor/sonata-project/google-authenticator/src/FixedBitNotation.php';
include_once 'vendor/sonata-project/google-authenticator/src/GoogleAuthenticatorInterface.php';
include_once 'vendor/sonata-project/google-authenticator/src/GoogleAuthenticator.php';
include_once 'vendor/sonata-project/google-authenticator/src/GoogleQrUrl.php';

class ModeleConnexion
{
    static private $bdd;


    function __construct()
    {
        $this->g = new \Sonata\GoogleAuthenticator\GoogleAuthenticator();
    }

    public function checkIfUsersExistsInActiveDirectory($mailUser, $password)
    {
        $userExists = false;
        $ldap_password = ADMIN_PASSWORD_READ_ONLY_OU_SOIGNANT;
        $ldap_username = ADMIN_READ_ONLY_OU_SOIGNANT;
        $ldap_connection = ldap_connect(IP_AD);
        ldap_set_option($ldap_connection, LDAP_OPT_PROTOCOL_VERSION, 3) or die('Unable to set LDAP protocol version');
        ldap_set_option($ldap_connection, LDAP_OPT_REFERRALS, 0); // We need this for doing an LDAP search.
        if (ldap_bind($ldap_connection, $ldap_username, $ldap_password)) {
            $result = ldap_search($ldap_connection, 'dc=mspr,dc=local', "(mail=$mailUser)", array('dn'), 0, 1);
            $entries = ldap_get_entries($ldap_connection, $result);
            if ($entries['count'] > 0) {
                $userExists = false;
                if (@ldap_bind($ldap_connection, $entries[0]['dn'], $password)) {
                    $userExists = true;
                }
            }
        }
        ldap_close($ldap_connection);
        return $userExists;
    }

    public function checkIfUsersExistsInBdd($mailUser)
    {
        $user = $this->checkIfUserExist($mailUser);
        $qrCode = false;
        if (!$user) {
            $qrCode = $this->addUser($mailUser);
        }
        return $qrCode;
    }

    public function getIp()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    public function getBrowser()
    {
        return $_SERVER['HTTP_USER_AGENT'];
    }

    public function checkIp($mailUser)
    {
        $ip = $this->getIpUser($mailUser);
        if ($ip != $this->getIp()) {
            $mail = new ModeleMail();
            $mail->sendMail($mailUser, 'Ip Changé', "Votre Ip à changé", "Votre Ip à changé");
            $this->updateIp($mailUser, $this->getIp());
        }
    }


    public function checkIfCodeGoogleAuthenticationIsCorrect($code, $mailUser)
    {
        $secret = $this->getUserSecret($mailUser);
        return $this->g->checkCode($secret, $code);
    }

    public function checkBrowser($mailUser)
    {
        $browser = $this->getBrowserUser($mailUser);
        if ($browser != $this->getBrowser()) {
            $this->blockAccount($mailUser);
            $secret = $this->getUserSecret($mailUser);
            $link = 'https://' . $_SERVER['HTTP_HOST'] . '/index.php?route=ControleurConnexion&action=unlockAccount&secret=' . $secret . '&newBrowser=' . $this->getBrowser() . '&mail=' . $mailUser;
            $mail = new ModeleMail();
            $mail->sendMail($mailUser, 'Vérification', "<a href='$link'>Vérifier navigateur</a>", $link);
        }
    }

    private function updateIp($mail, $ip)
    {
        self::$bdd = ModeleBdd::ConnexionBDD();
        try {
            $requete = self::$bdd->prepare("
            UPDATE users SET `ipUser` = :ipUser
            WHERE mailUser = :mailUser");
            $requete->bindParam(':ipUser', $ip);
            $requete->bindParam(':mailUser', $mail);
            $requete->execute();
            $requete = $requete->fetch();
        } catch (Exception $e) {
            die('Veuillez faire ' . $e->getMessage());
        }
        return $requete;
    }

    public function checkIfAccountBlocked($mailUser)
    {
        self::$bdd = ModeleBdd::ConnexionBDD();
        try {
            $requete = self::$bdd->prepare("
            SELECT locked
            FROM users
            WHERE users.mailUser = :mail");
            $requete->bindParam('mail', $mailUser);
            $requete->execute();
            $requete = $requete->fetch();
        } catch (Exception $e) {
            die('Veuillez faire ' . $e->getMessage());
        }
        return $requete['locked'];
    }

    private function getIpUser($mailUser)
    {
        self::$bdd = ModeleBdd::ConnexionBDD();
        try {
            $requete = self::$bdd->prepare("
            SELECT ipUser
            FROM users
            WHERE users.mailUser = :mail");
            $requete->bindParam('mail', $mailUser);
            $requete->execute();
            $requete = $requete->fetch();
        } catch (Exception $e) {
            die('Veuillez faire ' . $e->getMessage());
        }
        return $requete['ipUser'];
    }


    public function unlockBrowserAccount($secret, $newBrowser, $mail)
    {
        $isCorrect = $this->checkIfUserMailAndSecretAreCorrect($mail, $secret);
        if ($isCorrect) {
            $this->unblockAccountAndUpdateBrowser($mail, $newBrowser);
        } else {
            throw new Exception("ERREUR");
        }
        $link = 'https://' . $_SERVER['HTTP_HOST'] . '/index.php?route=ControleurConnexion&action=connexion';
        header('Location: '.$link);
    }

    private function unblockAccountAndUpdateBrowser($mail, $newBrowser)
    {
        self::$bdd = ModeleBdd::ConnexionBDD();
        try {
            $requete = self::$bdd->prepare("
            UPDATE users SET `locked` = NULL, `navigatorUser` = :navigatorUser
            WHERE mailUser = :mailUser");
            $requete->bindParam(':navigatorUser', $newBrowser);
            $requete->bindParam(':mailUser', $mail);
            $requete->execute();
            $requete = $requete->fetch();
        } catch (Exception $e) {
            die('Veuillez faire ' . $e->getMessage());
        }
        echo 'Compte débloqué';
        return $requete;
    }

    private function blockAccount($mail)
    {
        self::$bdd = ModeleBdd::ConnexionBDD();
        try {
            $now = new DateTime();
            $now = $now->format('Y-m-d H:i:s');
            $requete = self::$bdd->prepare("
            UPDATE users SET `locked` = 1
            WHERE mailUser = :mailUser");
            $requete->bindParam(':mailUser', $mail);
            $requete->execute();
            $requete = $requete->fetch();
        } catch (Exception $e) {
            die('Veuillez faire ' . $e->getMessage());
        }
        return $requete;
    }

    private function checkIfUserMailAndSecretAreCorrect($mailUser, $secret)
    {
        self::$bdd = ModeleBdd::ConnexionBDD();
        try {
            $requete = self::$bdd->prepare("
            SELECT *
            FROM users
            WHERE users.mailUser = :mail AND users.secretUser = :secret");
            $requete->bindParam('mail', $mailUser);
            $requete->bindParam('secret', $secret);
            $requete->execute();
            $requete = $requete->fetch();
        } catch (Exception $e) {
            die('Veuillez faire ' . $e->getMessage());
        }
        return $requete;
    }

    private function getBrowserUser($mailUser)
    {
        self::$bdd = ModeleBdd::ConnexionBDD();
        try {
            $requete = self::$bdd->prepare("
            SELECT navigatorUser
            FROM users
            WHERE users.mailUser = :mail");
            $requete->bindParam('mail', $mailUser);
            $requete->execute();
            $requete = $requete->fetch();
        } catch (Exception $e) {
            die('Veuillez faire ' . $e->getMessage());
        }
        return $requete['navigatorUser'];
    }

    private function getUserSecret($mailUser)
    {
        self::$bdd = ModeleBdd::ConnexionBDD();
        try {
            $requete = self::$bdd->prepare("
            SELECT secretUser
            FROM users
            WHERE users.mailUser = :mail");
            $requete->bindParam('mail', $mailUser);
            $requete->execute();
            $requete = $requete->fetch();
        } catch (Exception $e) {
            die('Veuillez faire ' . $e->getMessage());
        }
        return $requete['secretUser'];
    }

    private function checkIfUserExist($mail)
    {
        self::$bdd = ModeleBdd::ConnexionBDD();
        try {
            $requete = self::$bdd->prepare("
            SELECT *
            FROM users
            WHERE users.mailUser = :mail");
            $requete->bindParam('mail', $mail);
            $requete->execute();
            $requete = $requete->fetch();
        } catch (Exception $e) {
            die('Veuillez faire ' . $e->getMessage());
        }
        return $requete;
    }

    private function addUser($mailUser)
    {
        self::$bdd = ModeleBdd::ConnexionBDD();
        $ipUser = $this->getIp();
        $navigatorUser = $this->getBrowser();
        $secret = $this->g->generateSecret();
        try {
            $requete = self::$bdd->prepare("
            INSERT INTO `users` (`mailUser`, `ipUser`, `navigatorUser`, `secretUser`) 
            VALUES (:mailUser, :ipUser, :navigatorUser, :secretUser)");
            $requete->bindParam(':mailUser', $mailUser);
            $requete->bindParam(':ipUser', $ipUser);
            $requete->bindParam(':navigatorUser', $navigatorUser);
            $requete->bindParam(':secretUser', $secret);
            $requete->execute();
            $idUser =  self::$bdd->lastInsertId();
            $this->addBrutForce($idUser);
        } catch (Exception $e) {
            die('Veuillez faire ' . $e->getMessage());
        }
        return \Sonata\GoogleAuthenticator\GoogleQrUrl::generate($mailUser, $secret, 'MPSR');
    }

    private function addBrutForce($idUser)
    {
        self::$bdd = ModeleBdd::ConnexionBDD();
        $now = new DateTime();
        $now = $now->format('Y-m-d H:i:s');
        $one = "1";
        $zero = "0";
        $requeteBrutForce = self::$bdd->prepare("
            INSERT INTO `brutforce` (`idUser`, `firstRequest`, `lastRequest`, `numberRequest`, `blocked`) 
            VALUES (:idUser, :firstRequest, :lastRequest, :numberRequest, :blocked)");
        $requeteBrutForce->bindParam(':idUser', $idUser);
        $requeteBrutForce->bindParam(':firstRequest', $now);
        $requeteBrutForce->bindParam(':lastRequest', $now);
        $requeteBrutForce->bindParam(':numberRequest', $one);
        $requeteBrutForce->bindParam(':blocked', $zero);
        $requeteBrutForce->execute();
    }
}
