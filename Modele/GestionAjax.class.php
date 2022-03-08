<?php
/**
 * Class permettant de gérer l'ajax
 */
class GestionAjax
{

    public static $retourAjax;
    public static $codeRetourAjax;
    public static $optionJSONEncode;

    /**
     * Fonction qui est appellé pour envoyer la réposne ajax
     *
     * @return void
     */
    public static function sendResponseAjax()
    {
        Log::addLog("HTTP Code retour : ".self::$codeRetourAjax);
        header('Content-Type: application/json');
        http_response_code(self::$codeRetourAjax); 
        echo json_encode(self::$retourAjax, self::$optionJSONEncode);
    }
}
?>