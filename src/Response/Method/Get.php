<?php 
namespace Zewail\Api\Response\Method;

use Zewail\Api\Response\Method\HttpMethod;
use Zewail\Api\Http\Response;

/**
 * Get 的响应
 *
 * @author   Chan Zewail <chanzewail@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/czewail/think-api
 */
class Get extends HttpMethod
{
  	/**
     * 204 无内容响应
     *
     * @return think\Response
     */
    public function noContent()
    {
        $response = new Response(null);
        $response->setCode(204);
        return $response;
    }

    /**
     * 304 资源未更改（缓存）
     *
     * @return think\Response
     */
    public function notModified()
    {
        $response = new Response(null);
        $response->setCode(304);
        return $response;
    }

    /**
     * 406 服务端不支持所需表示
     *
     * @param string $message
     *
     * @throws think\exception\HttpException
     *
     * @return void
     */
    public function errorNotAcceptable($message = 'Not Acceptable')
    {
        $this->error($message, 406);
    }

    /**
     * 409 通用冲突错误响应
     *
     * @param string $message
     *
     * @throws think\exception\HttpException
     *
     * @return void
     */
    public function errorConflict($message = 'Conflict')
    {
        $this->error($message, 409);
    }

}