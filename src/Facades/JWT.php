<?php
namespace Zewail\Api\Facades;

use think\Facade;

class JWT extends Facade
{
	protected static function getFacadeClass()
    {
    	return 'Zewail\Api\JWT\Factory';
    }
}