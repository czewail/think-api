<?php 
namespace Zewail\Api\Serializers;

use Zewail\Api\Serializers\Serializer;

/**
 * @author   Chan Zewail <chanzewail@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/czewail/think-api
 */
class DataArraySerializer extends Serializer
{

	protected $content;

	protected $key = 'data';

	function __construct($content, $meta = [], $adds = [])
	{
		$this->content = $content;
		$this->setContent();
		parent::__construct($meta, $adds);
	}

	protected function setContent()
	{
		$this->data[$this->key] = $this->content;
	}
}