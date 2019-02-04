<?php
/**
 * Created by PhpStorm.
 * User: zerozelta
 * Date: 31/07/2018
 * Time: 10:17 PM
 */

namespace DFW\model;

use Illuminate\Database\Eloquent\Model;

class Credential extends Model {
    protected $table = "dfw_credentials";
    protected $fillable = ["name","description"];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function access(){
        return $this->belongsToMany(Access::class, 'dfw_access_credentials',
            'idCredential', 'idAccess');
    }

    /**
     * Assing one access to this credential
     * @param $access mixed
     */
    public function assignAccess($access){
        if(is_numeric($access)){
            $this->access()->attach($access);
        }elseif( $access instanceof Access){
            $this->access()->attach($access->id);
        }else if(is_string($access)){
            $acs = Access::getAccess($access);
            if($acs != null){
                $this->access()->attach($acs->id);
            }
        }
    }

    /**
     * Remove one access th this credential
     * @param $access mixed
     */
    public function removeAccess($access){
        if(is_numeric($access)){
            $this->access()->detach($access);
        }elseif( $access instanceof Access){
            $this->access()->detach($access->id);
        }else if(is_string($access)){
            $acs = Access::getAccess($access);
            if($acs != null){
                $this->access()->detach($acs->id);
            }
        }
    }

    /**
     * @param $access array|Access|integer|string
     * @return bool
     */
    public function checkAccess($access){
        if(is_array($access)){
            foreach ($access as $aelement){
                if($this->checkAccess($aelement)){
                    return true;
                }
            }
        }else{
            foreach ($this->access as $a){

                if(is_numeric($access)){
                    if($a->id == $access){
                        return true;
                    }
                }else if ($access instanceof Access){
                    if($a->id == $access->id){
                        return true;
                    }
                }else if (is_string($access)){
                    if($a->name == $access){
                        return true;
                    }
                }
            }
        }
        return false;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    public static function getCredential($credIdentifier){
        if(is_numeric($credIdentifier)){
            return Credential::find($credIdentifier);
        }else{
            return Credential::where('name', '=', $credIdentifier)->first();
        }
    }

    /**
     * @param $name
     * @param $description
     * @return Credential|null
     */
    public static function createCredential($name,$description){
        $cred = new Credential(["name" => $name,"description" => $description]);
        if($cred->save()){
            return $cred;
        }
        return null;
    }

    public static function deleteCredential($credIdentifier){
        $cred = Credential::getCredential($credIdentifier);
        return $cred->delete();
    }
}