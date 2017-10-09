<?php 
namespace Zewail\Api\Serializers;

/**
 * @author   Chan Zewail <chanzewail@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/czewail/think-api
 */
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

    public function get()
    {
        return $this->data;
    }
}