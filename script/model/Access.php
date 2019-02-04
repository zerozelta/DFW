<?php
/**
 * Created by PhpStorm.
 * User: zerozelta
 * Date: 31/07/2018
 * Time: 07:13 PM
 */

namespace DFW\model;

use Illuminate\Database\Eloquent\Model;

class Access extends Model {

    protected $table = "dfw_access";
    protected $fillable = ["name","description"];

    ////////////////////////////////////////////////

    /**
     * @param $accesIdentifier mixed
     * @return Access
     */
    public static function getAccess($accesIdentifier){
        if(is_numeric($accesIdentifier)){
            return Access::find($accesIdentifier);
        }else{
            return Access::where('name', '=', $accesIdentifier)->first();
        }
    }

    /**
     * @param $name
     * @param $description
     * @return Access|null
     */
    public static function createAccess($name,$description){
        $acs = new Access(["name" => $name,"description" => $description]);
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
        $acs = Access::getAccess($accesIdentifier);
        return $acs->delete();
    }


}