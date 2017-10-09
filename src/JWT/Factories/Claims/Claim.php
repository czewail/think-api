<?php 
namespace Zewail\Api\JWT\Factories\Claims;

/**
 * @author   Chan Zewail <chanzewail@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/czewail/think-api
 */
class Claim
{
    /**
     * name
     * 
     * @var string
     */
    protected $name;

    /**
     * value
     * 
     * @var string
     */
    protected $value;

    public function __construct($value)
    {
        $this->setValue($value);
    }
    /**
     * 设置Name
     *
     * @param  mixed  $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * 获取Name
     *
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * 设置Value
     *
     * @param  mixed  $name
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * 获取Value
     *
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * 转为数组
     * 
     * @return array
     */
    public function toArray()
    {
        return [$this->getName() => $this->getValue()];
    }
}