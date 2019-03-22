<?php
/**
 * Created by PhpStorm.
 * User: zerozelta
 * Date: 31/07/2018
 * Time: 07:49 PM
 */

namespace DFW;

use DFW;


class SecurityManager{

    const RULE_LOGGED_SESSION   = 0;
    const RULE_ACCESS           = 1;
    const RULE_CREDENTIAL       = 2;
    const RULE_POST_VARS_SETTED = 3;

    /**
     * @var ErrorBind[]
     */
    private static $securityRules = array(); // Listado de las actuales reglas de seguridad

    /**
     * @param $user
     * @param $access
     */
    public static function addAccessTo($user,$access){

    }

    /**
     * @param $user
     * @param $access
     */
    public static function removeAccessFrom($user,$access){

    }

    /**
     * @param $user
     * @param $credential
     */
    public static function addCredentialTo($user,$credential){

    }

    /**
     * @param $user
     * @param $credential
     */
    public static function removeCredentialFrom($user,$credential){

    }

    /**
     * Crea un enlace de restricciones de seguridad
     *
     *@param $index string
     * logged_session   @param $values:boolean verdadero o falso si se debe estar logueado o no
     * access           @param $values:string (separados por comas) Valores de DFW_ACCESS
     * credential       @param $values:string (separadas pro comas) valores de DFW_CREDENTIALS
     * data_set         @param $values:string (separados por comas) campos seteados en el field $_POST
     * module_access    @param $values:string
     *
     * @param $errorMessage string Mensaje de error que será asignado al error descriptión
     * PENDIENTE
     */
    public static function bind($index,$values,$errorMessage = null){
        $bind = new ErrorBind($index,$values,$errorMessage);
        self::$securityRules[] = $bind;
    }

    /**
     * comprueba todos los los accessos que se han enlazado con bind hasta ahora y en caso de incumplir alguno
     * hace un llamado a la función CI::makeJSON con statys -> error-access-denied , error-description -> {$errorMessage}
     * @param null $index si se desea checar y bincular simulataneamente un acceso
     * @param null $values
     * @param null $errorMessage
     */
    public static function check($index = null,$values = null,$errorMessage = null){
        if($index != null && $values != null){
            self::bind($index,$values,$errorMessage);
        }

        for($i = 0;$i < count(self::$securityRules);$i++){
            $bind = &self::$securityRules[$i];

            if($bind->checked == true){
                continue;   // Si el acceso ya fue checado saltamos esta regla
            }

            switch($bind->index){
                case self::RULE_LOGGED_SESSION:{
                    if($bind->errorMessage == null){ $bind->errorMessage = "this action require a logged user"; }
                    if(DFW::isLogged() !== $bind->values){
                        DFW::makeJSON("error-access-denied",["error-description" => $bind->errorMessage]);
                    }
                    break;
                }
                case self::RULE_ACCESS:{
                    if($bind->errorMessage == null){ $bind->errorMessage = "you don't have the required access"; }
                    if(DFW::isLogged() == false || DFW::SESSION_USER()->checkAccess($bind->values) == false){
                        DFW::makeJSON("error-access-denied",array("error-description" => $bind->errorMessage));
                    }
                    break;
                }
                case self::RULE_CREDENTIAL:{
                    if($bind->errorMessage == null){ $bind->errorMessage = "you don't have the required credential"; }
                    if(DFW::isLogged() == false || DFW::SESSION_USER()->checkCredential($bind->values) == false) {
                        DFW::makeJSON("error-access-denied",array("error-description" => $bind->errorMessage));
                    }
                    break;
                }
                case self::RULE_POST_VARS_SETTED:{
                    if($bind->errorMessage == null){ $bind->errorMessage = "undefined POST vars required"; }
                    if(!DFW\Utils::isDataSet($_POST,$bind->values)){
                        DFW::makeJSON("error-access-denied",array("error-description" => $bind->errorMessage , "post-vars-required" => $bind->values));
                    }
                    break;
                }
            }

            $bind->checked = true;
        }
    }

    /**
     * Retorna el listado de reglas de seguridad actual
     */
    public static function getRules(){
        return var_dump(self::$securityRules);
    }

    public static function getRulesJSON(){
        $a = array();
        for($i = 0; $i < count(self::$securityRules) ; $i++){
            $a[] = get_object_vars(self::$securityRules[$i]);
        }

        return $a;
    }

    /**
     * @param $text
     * @param string $key
     * @return string
     */
    public static function encrypt($text,$key){
        return openssl_encrypt ($text, "aes128", $key);
    }

    /**
     * @param $cryptext
     * @param string $key
     * @return string
     */
    public static function decrypt($cryptext,$key){
        return openssl_encrypt ($cryptext, "aes128", $key);
    }

}

class  ErrorBind{
    public $index;
    public $values;
    public $errorMessage;

    public $checked = false;

    public function __construct($index,$values,$errorMessage){
        $this->index = $index;

        if(is_string($values)){
            $this->values = explode(",",$values);
        }else{
            $this->values = $values;
        }

        $this->errorMessage = $errorMessage;
    }
}