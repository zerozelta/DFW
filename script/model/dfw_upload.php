<?php
/**
 * Created by PhpStorm.
 * User: zerozelta
 * Date: 31/03/2019
 * Time: 02:57 PM
 */

namespace DFW\model;


use Illuminate\Database\Eloquent\Model;

class dfw_upload extends  Model{
    protected $table = "dfw_uploads";
    protected $fillable = ["slug" ,"source" , "extension","downloads","validated","idUser"];

    public function getSlug(){
        return $this->slug;
    }

    public function getSource(){
        return $this->source;
    }

    public function getExtension(){
        return $this->extension;
    }

    public function getDownloads(){
        return $this->downloads;
    }

    public function isValid(){
        return $this->validated;
    }

    public function getIdUser(){
        return $this->idUser;
    }


    public function setSlug($slug){
        $this->slug = $slug;
    }

    public function setSource($source){
        $this->source = $source;
    }

    public function setExtension($ext){
        $this->extension = $ext;
    }

    public function setDownloads($downloads){
        $this->downloads = $downloads;
    }

    public function setValidated($valid){
        $this->validated = $valid;
    }

    public function setIdUser($id){
        $this->idUser = $id;
    }
}