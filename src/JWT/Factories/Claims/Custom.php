<?php 
namespace Zewail\Api\JWT\Factories\Claims;

/**
 * @author   Chan Zewail <chanzewail@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/czewail/think-api
 */
class Custom extends Claim
{
    protected $name;

    public function __construct($name, $value)
    {
        $this->name = $name;
        parent::__construct($value);
    }
}
