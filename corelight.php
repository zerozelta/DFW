<?php
/**
 * DFW www.scefira.com
 * Author: zerozelta
 */

const VERSION = "0.2.1";

define("DFW_TIME_START", microtime(true));  // DFW Start time

$DFW_HOST 		= 	$_SERVER["HTTP_HOST"];													    // zephyra.com.mx
$DFW_ROOT 		= 	dirname(__FILE__);													// C:/zephyra/dfw
$DFW_ROOT_DIR 	= 	dirname(dirname(__FILE__));											// C:/zephyra
$DFW_PATH 		= 	substr($DFW_ROOT,strlen($_SERVER["DOCUMENT_ROOT"]),strlen($DFW_ROOT));	// zephyra/dfw

$DFW_HOST 	= strtolower(str_replace("\\","/",$DFW_HOST));
$DFW_ROOT 	= strtolower(str_replace("\\","/",$DFW_ROOT));
$DFW_PATH 	= strtolower(str_replace("\\","/",$DFW_PATH));

if(substr($DFW_PATH,0,1) == "/"){ $DFW_PATH = substr($DFW_PATH,1); }
if($DFW_PATH == ""){ $DFW_PATH = "."; }

$DFW_DIR 		= pathinfo($DFW_PATH)["dirname"];											    // zephyra

define("DFW_HOST",$DFW_HOST);
define("DFW_ROOT",$DFW_ROOT);
define("DFW_ROOT_DIR",$DFW_ROOT_DIR);
define("DFW_PATH",$DFW_PATH);
define("DFW_DIR",$DFW_DIR);

require_once(DFW_ROOT . "/script/dfw.php");                                     // DFW System (includes an DFW autoloader )
require_once(DFW_ROOT . "/lib/illuminate/database/vendor/autoload.php");     // Modulo de base de datos Illuminate/Elocuent
?>