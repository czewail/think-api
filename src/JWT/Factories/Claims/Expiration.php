<?php
namespace Zewail\Api\JWT\Factories\Claims;

use Config;

/**
 * @author   Chan Zewail <chanzewail@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/czewail/think-api
 */
class Expiration extends Claim
{
    /**
     * Name
     */
    protected $name = 'exp';

}
