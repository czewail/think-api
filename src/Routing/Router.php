<?php
namespace Zewail\Api\Routing;

use Config;
use Request;
use Closure;
use Zewail\Api\Setting\Set;

/**
 * @author   Chan Zewail <chanzewail@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/czewail/think-api
 */
class Router
{
    // 路由版本列表
    private static $versions = [];

    protected $config = [];


    public function __construct()
    {
        Set::api(function($config) {
            $this->config = $config;
        });
    }

    /**
     * 创建版本
     * 
     * @param  [type]  $version
     * @param  Closure $routes 
     */
    public function version($version = null, Closure $routes)
    {
        if (is_array($version)) {
            self::$versions = $version;
        }
        if (is_string($version)) {
            self::$versions = [$version];
        }
        array_unique(self::$versions);

        $_version = Request::header('Api-Version') ?: Request::param('version');

        if (!empty($_version)) {
            if (in_array($_version, self::$versions) ) {
                call_user_func_array($routes, []);
            }
        } else {
            if ($this->config['version']) {
                $default_version = $this->config['version'];
                if (in_array($default_version, self::$versions) ) {
                    call_user_func_array($routes, []);
                }
            }
        }
    }
}