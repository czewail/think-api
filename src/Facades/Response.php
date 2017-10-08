<?php 
namespace Zewail\APi\Facades;

use think\Facade;

class Response extends Facade
{
	protected static function getFacadeClass()
    {
    	return 'Zewail\Api\Response\Factory';
    }
}