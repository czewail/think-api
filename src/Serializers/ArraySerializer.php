<?php 
namespace Zewail\Api\Serializers;

use Zewail\Api\Serializers\Serializer;

class ArraySerializer extends Serializer
{

	protected $content;

	function __construct($content, $meta = [], $adds = [])
	{
		$this->content = $content;
		$this->setContent();
		parent::__construct($meta, $adds);
	}

	protected function setContent()
	{
		$this->data = $this->content;
	}
}