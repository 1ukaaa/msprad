<?php
/**
 * Log class
 */
class Log 
{
    protected static $nomDossier;
    protected static $nomFichier;
    protected static $reculDossier;

    /**
     * Constructeur de la classe Log
     */
    public static function initialize()
    {
        self::$nomDossier = NOM_DOSSIER_LOGS;
        self::$nomFichier = date("Y-m-d");
        self::$reculDossier = RECUL_DOSSIER_LOG;
        self::createDossierLog();
    }
    /**
     * Fonction qui créer les dossiers de log si ils n'existent pas 
     */
    private static function createDossierLog()
    {
        if(!is_dir(self::$reculDossier.self::$nomDossier))
        {
            mkdir(self::$reculDossier.self::$nomDossier."/",0777,true);
        }
    }
    /**
     * Fonction qui ajoute une ligne de Log
     * @param mixed $data les datas a ecrire dans le log
     */
    public static function addLog($data)
    {
        $data = is_array($data) ? json_encode($data) : $data;
        if(strlen($data) > 10000)
        {
            $data = "Data trop volumineuse pour les logs";
        }
        self::writeLineLog($data);
    }
    /**
     * Fonction qui écrit une ligne de Log
     * @param string $ligne la ligne a ecrire dans le log
     */
    private static function writeLineLog($ligne)
    {
        $heure = new DateTime;
        file_put_contents(self::$reculDossier.self::$nomDossier."/".self::$nomFichier.".log","\n". "[".$heure->format("H:i:s.v")."] ". $ligne , FILE_APPEND);
    }
}