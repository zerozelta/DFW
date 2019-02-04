<?php
/**
 * Created by PhpStorm.
 * User: zerozelta
 * Date: 31/07/2018
 * Time: 11:30 AM
 */

namespace DFW\model;


use DFW\model\Access;
use DFW\model\Credential;
use Illuminate\Database\Eloquent\Model;

class User extends Model {

    protected $table = "dfw_users";
    protected $fillable = ["nick","email","encodedKey"];

    public function credentials(){
        return $this->belongsToMany(Credential::class, 'dfw_users_credentials',
            'idUser', 'idCredential');
    }

    public function assignCredential($credential){
        if(is_numeric($credential)){
            $this->credentials()->attach($credential);
        }elseif( $credential instanceof Credential){
            $this->credentials()->attach($credential->id);
        }else if(is_string($credential)){
            $cred = Credential::getCredential($credential);
            if($cred != null){
                $this->credentials()->attach($cred->id);
            }
        }
    }

    public function removeCredential($credential){
        if(is_numeric($credential)){
            $this->credentials()->detach($credential);
        }elseif( $credential instanceof Credential){
            $this->credentials()->detach($credential->id);
        }else if(is_string($credential)){
            $cred = Credential::getCredential($credential);
            if($cred != null){
                $this->credentials()->detach($cred->id);
            }
        }
    }


    /**
     * @param $access array|string|Access|integer
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
     * @return bool
     */
    public function checkCredential($credential){
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
                }else if($credential instanceof Credential){
                    if($credential->id == $cred->id){
                        return true;
                    }
                }else{
                    $c = Credential::getCredential($cred);
                    if($c != null && $c->id == $credential->id){
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
     * @return User|null
     */
    public static function getUser($user,$eagerLoad = []){
        if($user == null){
            return null;
        }
        if(is_numeric($user)){
            return User::with($eagerLoad)->find($user);
        }else if(filter_var($user, FILTER_VALIDATE_EMAIL)){
            return User::with($eagerLoad)->where('email', '=', $user)->first();
        }else{
            return User::with($eagerLoad)->where('nick', '=', $user)->first();
        }
    }

    /**
     * Create new user with the params nick, email, and encodeKey
     * @param $nick
     * @param $email
     * @param $key
     * @return User the user object can be manipulated
     */
    public static function createUser($nick, $email, $key){
        $password = "";
        if($key != null && $key != ""){
            $password = password_hash($key,PASSWORD_BCRYPT);
        }

        $user = new User([
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
        $user = User::get($userIdentifier);
        if($user != null){
            return $user->delete();
        }
    }

    /**
     * @param $userIdentifier
     * @return bool
     */
    public static function isExists($userIdentifier){
        $user = User::get($userIdentifier);
        return $user != null;
    }

}