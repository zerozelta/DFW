<?php
/**
 * Created by PhpStorm.
 * dfw_user: zerozelta
 * Date: 31/07/2018
 * Time: 07:13 PM
 */

namespace DFW\model;

use Illuminate\Database\Eloquent\Model;

class dfw_access extends Model {

    protected $table = "dfw_access";
    protected $fillable = ["name","description"];

    ////////////////////////////////////////////////

    /**
     * @param $accesIdentifier mixed
     * @return dfw_access
     */
    public static function getAccess($accesIdentifier){
        if(is_numeric($accesIdentifier)){
            return dfw_access::find($accesIdentifier);
        }else{
            return dfw_access::where('name', '=', $accesIdentifier)->first();
        }
    }

    /**
     * @param $name
     * @param $description
     * @return dfw_access|null
     */
    public static function createAccess($name,$description){
        $acs = new dfw_access(["name" => $name,"description" => $description]);
        if($acs->save()){
            return $acs;
        }
        return null;
    }

    /**
     * @param $accesIdentifier
     * @return bool|null
     * @throws \Exception
     */
    public static function deleteAccess($accesIdentifier){
        $acs = dfw_access::getAccess($accesIdentifier);
        return $acs->delete();
    }


}