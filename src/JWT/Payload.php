<?php 
namespace Zewail\Api\JWT;

use Zewail\Api\JWT\Factories\Claims\Collection;

class Payload 
{
	private $claims;
	
	public function __construct(Collection $claims)
	{
		$this->claims = $claims;
	}

	// 转为数组
	public function toArray()
    {
        return $this->claims->toArray();
    }

}