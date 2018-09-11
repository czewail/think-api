<?php 
namespace Zewail\Api\Setting;

use Config;

/**
 * @author   Chan Zewail <chanzewail@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/czewail/think-api
 */
class Set
{
    protected static $files = [
        'resources' => 'resources.php',
        'api' => 'api.php',
        'jwt' => 'jwt.php',
    ];

    public static function __callStatic($func, $args)
    {
        $path = dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR;
        if ($file = self::$files[$func]) {
            $config = require($path . $file);
            $_config = Config::pull($func);
            if ($_config && is_array($_config)) {
                $config = array_merge($config, $_config);
            }
            call_user_func_array($args[0], [$config]);
        }
    }
}