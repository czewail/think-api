<?php
namespace Zewail\Api\Facades;

use think\Facade;

class ApiRoute extends Facade
{
	protected static function getFacadeClass()
    {
    	return 'Zewail\Api\Routing\Router';
    }
}