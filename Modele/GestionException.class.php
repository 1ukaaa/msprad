<?php
/**
 * Class permettant de gérer les erreurs
 */
class GestionException
{
    protected $dataException;

    public function __construct()
    {
        @set_exception_handler(array($this, 'attraperException'));
    }

    /**
     * Fonction qui est appellé quand un exception est levé
     *
     * @param [Exception] $exception
     * @return void
     */
    public function attraperException($exception)
    {
        $this->dataException=array(
            "file"=>$exception->getFile(),
            "line"=>$exception->getLine(),
            "code"=>$exception->getCode(),
            "message"=>$exception->getMessage(),
            "trace"=>json_encode($exception->getTrace()),
            "debug"=>json_encode(debug_backtrace()));
        $this->ecrireExceptionDansLog();
    }

    /**
     * Permet d'écire des erreurs dans les logs
     *
     * @return void
     */
    public function ecrireExceptionDansLog()
    {
        Log::addLog("Une erreur s'est produite : ");
        Log::addLog("Dans ce fichier : ".$this->dataException["file"]);
        Log::addLog("A cette ligne : ".$this->dataException["line"]);
        Log::addLog("Numero de l'erreur : ".$this->dataException["code"]);
        Log::addLog("Message de l'erreur : ".$this->dataException["message"]);
        Log::addLog("Trace de l'erreur : ".$this->dataException["trace"]);
        Log::addLog("Debug : ".$this->dataException["debug"]);
    }
}