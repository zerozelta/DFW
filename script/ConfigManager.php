<?php
/**
 * dfw_user: zerozelta
 * Date: 26/07/2018
 * Time: 11:14 AM
 */

namespace DFW;

use DFW\ConfigScript;

class ConfigManager{

    /**
     * @var ConfigScript
     */
    public static $GENERAL;

    /**
     * @param string $config array|string|null
     */
    public static function initialice($config = "general.json"){
        self::$GENERAL = new ConfigScript($config);
    }
}
