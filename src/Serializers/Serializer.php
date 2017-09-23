<?php 
namespace Zewail\Api\Serializers;

class Serializer
{
	protected $meta;
	protected $adds = [];

	public $data = [];

	public function __construct($meta = [], $adds = [])
	{
		$this->meta = $meta;
		$this->adds = $adds;
		$this->setMeta()->setAdds();
	}

	protected function setMeta()
	{
		if (!empty($this->meta)) {
			$this->data['meta'] = $this->meta;
		}
		return $this;
	}

	protected function setAdds()
	{
		foreach ($this->adds as $key => $value) {
			if (!empty($value)) {
				$this->data[$key] = $value;
			}
		}
		return $this;
	}

	public function getData()
	{
		return $this->data;
	}
}