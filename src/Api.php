<?php 
namespace ysz\Api;

use ysz\Api\Response\Factory as ResponseFactory;
use ysz\Api\JWT\Factory as JWTFactory;
use think\Config;

/**
 * @author   Sherlock yang <452025156@qq.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/ysz/think-api
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
