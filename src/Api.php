<?php 
namespace Zewail\Api;

use Zewail\Api\Response\Factory as ResponseFactory;
use think\Config;


trait Api
{

	protected $response;

	function __construct()
	{
		$this->response();
	}


	/**
	 * 获取 Response 对象
	 * @return [type]
	 */
	
	protected function response() {
		$this->response = new ResponseFactory;
	}
}