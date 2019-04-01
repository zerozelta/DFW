<?php
/**
 * Created by PhpStorm.
 * User: zerozelta
 * Date: 30/03/2019
 * Time: 11:34 PM
 */

namespace SC;

use DFW;

define("DFW_UPLOAD_DIR",DFW::cfg("UPLOAD_PATH",DFW_ROOT_DIR . "/upload"));

class UploadManager{

    /**
     * @param $file array array asociative of uploaded file
     * @param null $slug
     * @param string $uploadPath
     * @param bool $confirmed
     * @return DFW\model\dfw_upload|null
     */
    public static function saveUpload($file,$slug = null,$uploadPath = "/",$confirmed = true){
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);

        if($slug == null){
            $slug = DFW\UUID::v4();
        }else{
            $slug = DFW\Utils::toSlug($slug);
        }

        $name = pathinfo($file['name'], PATHINFO_FILENAME);

        $path = DFW_UPLOAD_DIR . $uploadPath . $name . "."  . $ext;

        if(!move_uploaded_file($file['tmp_name'],$path)){
            return null;
        }

        $upload = new DFW\model\dfw_upload();
        $upload->slug = $slug;
        $upload->extension = $ext;
        $upload->source = $path;
        $upload->confirmed = $confirmed;
        $upload->downloads = 0;


        if($upload->save()){
            return $upload;
        }

        return null;
    }

    /**
     * @param $upload string|DFW\model\dfw_upload id or dfw_upload object to validate
     * @return bool
     */
    public static function validateUploadFile($upload){
        if($upload == false){
            return false;
        }

        if(is_numeric($upload)){
            $upload = DFW\model\dfw_upload::find($upload);
        }else if($upload instanceof DFW\model\dfw_upload === false){
            return false;
        }

        /**
        * @var $upload DFW\model\dfw_upload
        */

        $upload->setValidated(true);

        return $upload->save();
    }

    /**
     * @return mixed|null|object
     */
    public static function getUploadDir(){
        return DFW_UPLOAD_DIR;
    }
}