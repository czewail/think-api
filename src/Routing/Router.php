<?php
namespace Zewail\Api\Routing;

use think\Route;
use think\Config;
use think\Request;
use Closure;

/**
 * @author   Chan Zewail <chanzewail@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/czewail/think-api
 */
class Router extends Route
{
	// 路由版本列表
	private static $versions = [];

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

		$_version = Request::instance()->header('api-version') ?: Request::instance()->param('api-version');

		if (!empty($_version)) {
			if (in_array($_version, self::$versions) ) {
        		call_user_func_array($routes, [$this]);
        	}
		} else {
	        if (Config::has('api.api_version')) {
	        	$default_version = Config::get('api.api_version');
	        	if (in_array($default_version, self::$versions) ) {
	        		call_user_func_array($routes, [$this]);
	        	}
	        }
		}
	}
}