<?php 
namespace Zewail\Api\Response\Method;

use Zewail\Api\Response\Method\HttpMethod;
use Zewail\Api\Http\Response;

/**
 * Post 的响应
 *
 * @author   Chan Zewail <chanzewail@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/czewail/think-api
 */
class Post extends HttpMethod
{
  	/**
     * 201 创建了资源的响应
     *
     * @param  资源响应位置
     * @param  资源响应内容
     * @return think\Response
     */
    public function created($location = null, $content = null)
    {
        $response = new Response($content);
        $response->setCode(201);
        if (! is_null($location)) {
            $response->addHeader('Location', $location);
        }
        return $response;
    }
    
  	/**
     * 202 已接受处理请求但尚未完成（异步处理）
     *
     * @param  资源响应位置
     * @param  资源响应内容
     * @return think\Response
     */
    public function accepted($message = 'Accepted')
    {
        $response = new Response($message);
        $response->setCode(202);
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

    /**
     * 412 前置条件失败（如执行条件更新时的冲突）
     *
     * @param string $message
     *
     * @throws think\exception\HttpException
     *
     * @return void
     */
    public function errorPreconditionFailed($message = 'Precondition Failed')
    {
        $this->error($message, 412);
    }

    /**
     * 415 接受到的表示不受支持
     *
     * @param string $message
     *
     * @throws think\exception\HttpException
     *
     * @return void
     */
    public function errorUnsupportedMedia($message = 'Unsupported Media Type')
    {
        $this->error($message, 415);
    }

}