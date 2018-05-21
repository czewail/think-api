<?php 
namespace Zewail\Api;

use Zewail\Api\Response\Factory as ResponseFactory;
use Zewail\Api\JWT\Factory as JWTFactory;
use think\Config;

/**
 * @author   Chan Zewail <chanzewail@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/czewail/think-api
 */
trait Api
{

    protected $response;
    protected $jwt;

    function __construct()
    {
        $this->init();
    }

    protected function init() {
        $this->response = new ResponseFactory;
        $this->jwt = new JWTFactory;
    }
}