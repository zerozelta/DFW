<?php
/**
 * Created by PhpStorm.
 * dfw_user: zerozelta
 * Date: 31/07/2018
 * Time: 10:17 PM
 */

namespace DFW\model;

use Illuminate\Database\Eloquent\Model;

class dfw_credential extends Model {
    protected $table = "dfw_credentials";
    protected $fillable = ["name","description"];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function access(){
        return $this->belongsToMany(dfw_access::class, 'dfw_access_credentials',
            'idCredential', 'idAccess');
    }

    /**
     * Assing one access to this credential
     * @param $access mixed
     */
    public function assignAccess($access){
        if(is_numeric($access)){
            $this->access()->attach($access);
        }elseif( $access instanceof dfw_access){
            $this->access()->attach($access->id);
        }else if(is_string($access)){
            $acs = dfw_access::getAccess($access);
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
        }elseif( $access instanceof dfw_access){
            $this->access()->detach($access->id);
        }else if(is_string($access)){
            $acs = dfw_access::getAccess($access);
            if($acs != null){
                $this->access()->detach($acs->id);
            }
        }
    }

    /**
     * @param $access array|dfw_access|integer|string
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
                }else if ($access instanceof dfw_access){
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
            return dfw_credential::find($credIdentifier);
        }else{
            return dfw_credential::where('name', '=', $credIdentifier)->first();
        }
    }

    /**
     * @param $name
     * @param $description
     * @return dfw_credential|null
     */
    public static function createCredential($name,$description){
        $cred = new dfw_credential(["name" => $name,"description" => $description]);
        if($cred->save()){
            return $cred;
        }
        return null;
    }

    public static function deleteCredential($credIdentifier){
        $cred = dfw_credential::getCredential($credIdentifier);
        return $cred->delete();
    }
}