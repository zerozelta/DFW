<?php
/**
 * Created by PhpStorm.
 * dfw_user: zerozelta
 * Date: 31/07/2018
 * Time: 11:30 AM
 */

namespace DFW\model;


use DFW\model\dfw_access;
use DFW\model\dfw_credential;
use Illuminate\Database\Eloquent\Model;

class dfw_user extends Model {

    protected $table = "dfw_users";
    protected $fillable = ["nick","email","encodedKey"];

    public function credentials(){
        return $this->belongsToMany(dfw_credential::class, 'dfw_users_credentials',
            'idUser', 'idCredential');
    }

    public function assignCredential($credential){
        if(is_numeric($credential)){
            $this->credentials()->attach($credential);
        }elseif( $credential instanceof dfw_credential){
            $this->credentials()->attach($credential->id);
        }else if(is_string($credential)){
            $cred = dfw_credential::getCredential($credential);
            if($cred != null){
                $this->credentials()->attach($cred->id);
            }
        }
    }

    public function removeCredential($credential){
        if(is_numeric($credential)){
            $this->credentials()->detach($credential);
        }elseif( $credential instanceof dfw_credential){
            $this->credentials()->detach($credential->id);
        }else if(is_string($credential)){
            $cred = dfw_credential::getCredential($credential);
            if($cred != null){
                $this->credentials()->detach($cred->id);
            }
        }
    }

    /**
     * @param $access array|string|dfw_access|integer
     * @return bool verdadero o falso si exite el acceso
     */
    public function checkAccess($access){
        if($this->id == 1){
            return true;    // El usuario 1 es SUPERADMIN
        }
        if(is_array($access)){
            foreach ($access as $aelemnt){
                if($this->checkAccess($aelemnt)){
                    return true;    // Añade recursividad hasta encontrar una coincidencia
                };
            }
        }else{
            foreach ($this->credentials AS $cred){
                if($cred->checkAccess($access)){
                    return true;
                }
            }
            return false;
        }
    }

    /**
     * @param $credential
     * @return bool
     */
    public function checkCredential($credential){

        if($credential === null){
            return false;
        }

        if($this->id == 1){
            return true;    // El usuario 1 es SUPERADMIN
        }

        if(is_array($credential)){
            foreach ($credential as $celemnt){
                if($this->checkCredential($celemnt)){
                    return true;    // Añade recursividad hasta encontrar una coincidencia
                };
            }
        }else{
            foreach ($this->credentials AS $cred){
                if(is_numeric($credential)){
                    if($credential == $cred->id){
                        return true;
                    }
                }else if($credential instanceof dfw_credential){
                    if($credential->id == $cred->id){
                        return true;
                    }
                }else{
                    if($cred->name == $credential){
                        return true;
                    }
                }
            }

        }
        return false;
    }


    ///////////////////////////////////////////////////////////////////

    /**
     * @param $user
     * @param array $eagerLoad array preload object (example: credentials) and subobjects (separated with a '.' example: credentials.access)
     * @return dfw_user|null
     */
    public static function getUser($user,$eagerLoad = []){
        if($user == null){
            return null;
        }
        if(is_numeric($user)){
            return dfw_user::with($eagerLoad)->find($user);
        }else if(filter_var($user, FILTER_VALIDATE_EMAIL)){
            return dfw_user::with($eagerLoad)->where('email', '=', $user)->first();
        }else{
            return dfw_user::with($eagerLoad)->where('nick', '=', $user)->first();
        }
    }

    /**
     * Create new user with the params nick, email, and encodeKey
     * @param $nick
     * @param $email
     * @param $key
     * @return dfw_user the user object can be manipulated
     */
    public static function createUser($nick, $email, $key){
        $password = "";
        if($key != null && $key != ""){
            $password = password_hash($key,PASSWORD_BCRYPT);
        }

        $user = new dfw_user([
            "nick" => $nick,
            "email" => $email,
            "encodedKey" => $password
        ]);

        if($user->save()) {
            return $user;
        }else{
            return null;
        }
    }

    /**
     * @param $userIdentifier
     * @throws \Exception
     * @return bool|null
     */
    public static function deleteUser($userIdentifier){
        $user = dfw_user::get($userIdentifier);
        if($user != null){
            return $user->delete();
        }
        return false;
    }

    /**
     * @param $userIdentifier
     * @return bool
     */
    public static function isExists($userIdentifier){
        $user = dfw_user::get($userIdentifier);
        return $user != null;
    }

}