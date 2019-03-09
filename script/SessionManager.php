<?php
/**
 * Created by PhpStorm.
 * User: zerozelta
 * Date: 28/07/2018
 * Time: 09:21 AM
 */

namespace DFW;

use DFW\model\Session;
use DFW\model\User;
use DFW\Utils;
use DFW\ConfigManager;
use DFW\DatabaseManager;
use DFW\UUID;

class SessionManager{

    public static $sid; // Nombre de la cookie SID
    public static $stk; // Nombre de la cookie STK

    /**
     * @var \DFW\model\Session
     */
    private static $session;

    /**
     * @var User
     */
    private static $sessionUser;

    /**
     * Funcion de inicialización de la sessión (Se inicia automaticamente al cargar el modulo)
     * @param string $sid name of session id cookie
     * @param string $stk name of session token cookie
     */
    public static function initialice($sid = "sid",$stk = "stk"){
        self::$stk = $stk;
        self::$sid = $sid;

        $code = null;
        $token = null;
        if(isset($_COOKIE[$sid]) && isset($_COOKIE[$stk]) && is_numeric($_COOKIE[$sid])){
            self::$session = Session::find($_COOKIE[$sid]);
            $token = $_COOKIE[$stk];
        }else{
            self::$session = self::regenerateSession(); // Se crea un nuevo registro de sessión
            $token = self::$session->token;
        }

        if(self::$session== null || self::$session->token != $token){
            self::regenerateSession(); // Se crea un nuevo registro de sessión
        }

        DatabaseManager::get()->getConnection()->table("dfw_sessions")->where("id","=",self::$session->id)->update(array(
            "expire" => Date("Y-m-d h-i",time() + (86400 * ConfigManager::$GENERAL->get("SESSION/TIME_EXPIRATION",7))),
            "site" => $_SERVER["REQUEST_URI"]
        ));

        if(rand(1,20) == 10){   // Limpiamos registros cada 20 peticiones aprox.
            self::swept();      // Limpiado de valores obsoletos de la base de datos
        }
    }


    /**
     * check if the session is opened
     * @return bool
     */
    public static function isLogged(){
        return self::$session != null && self::$session->idUser != null && self::$session->idUser != 0;
    }

    public static function getUser(){
        if(self::isLogged() == false){
            return null;
        }

        if(self::$sessionUser == null){
            self::$sessionUser = User::with("credentials.access")->where("id",self::$session->idUser)->first();
        }

        return self::$sessionUser;
    }

    /**
     * close session
     */
    public static function logout(){
        if(self::isLogged()){
            try{
                self::$session->delete();
            }catch (\Exception $e){}
            self::setCookie(self::$sid,"",0);
            self::setCookie(self::$stk,"",0);
            self::$session = null;
        }
    }

    public static function login($userIdentifier,$password,$remember = false){

        sleep(1); // It dificults login force attack

        if($userIdentifier == null || $userIdentifier == ""){ // Seguridad, validación de campos de identificador vacio
            return false;
        }

        $user = User::getUser($userIdentifier,"credentials.access");

        if($user != null){
            if((($user->encodedKey == null || $user->encodedKey == "") && $password == "") || password_verify($password,$user->encodedKey)){
                self::$session->idUser = $user->id;
                self::$session->save(); // Save the user Id in the session record

                self::$sessionUser = $user;    // stablish the user object to sessionUser object
                return true;
            }
        }

        return false;
    }

    ///////////////////////////////////////////////

    /**
     * @return string new unique SID
     */
    private static function generateToken(){
        return md5(UUID::v4());
    }

    /**
     * Get the Token for this session
     * @return mixed
     */
    public static function getSessionToken(){
        return self::$session->getAttribute("token");
    }

    /**
     * Get the Token for this session
     * @return mixed
     */
    public static function getSessionId(){
        return self::$session->getAttribute("id");
    }

    /**
     * Regenerate the session record on database with a new sid for the current connection
     * @param int $time
     * @return Session|null
     */
    private static function regenerateSession($time = 0){
        $code = self::generateToken();
        $ip = Utils::getClientIP();
        $agent = $_SERVER['HTTP_USER_AGENT'];

        $session = new Session([
            'token' => $code,
            'agent' => $agent,
            'ip' => $ip,
            'expire' => date("Y-m-d h-i",(time()+604800)) // 7 dias extra de caducidad para session en base de datos
        ]);


        if(!$session->save() || !self::setCookie(self::$sid,$session->id,$time) || !self::setCookie(self::$stk,$code,$time)){
            return null;
        }

        self::$session = $session;

        return $session;
    }


    /**
     * @param $name string
     * @param $value string
     * @param int $time
     * @param bool $secure
     * @param bool $httponly
     * @return bool
     */
    public static function setCookie($name,$value,$time = 0,$secure = false,$httponly = false){
        $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;
        if($time > 0){
            $time = time() + $time;
        }

        $domain = $_SERVER['HTTP_HOST'];
        return setcookie($name,$value,$time);
    }


    /**
     * Clean the expired sessions
     */
    private static function swept(){
        DatabaseManager::get()->getConnection()->delete("DELETE FROM dfw_sessions WHERE :now > dfw_sessions.expire",["now" => Date("Y-m-d h-i",time())]);
    }
}