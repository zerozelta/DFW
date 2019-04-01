<?php
/**
 * Created by PhpStorm.
 * dfw_user: zerozelta
 * Date: 28/07/2018
 * Time: 08:54 PM
 */

namespace DFW\model;


use Illuminate\Database\Eloquent\Model;

class dfw_session extends  Model {
    protected $table = "dfw_sessions";
    protected $fillable = ["token" , "agent" , "ip" , "expire"];
}