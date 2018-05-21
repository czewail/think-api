<?php 
namespace Zewail\Api\Response\Method;

use Zewail\Api\Exceptions\ResponseException;
use Zewail\Api\Http\Response;

/**
* 
*/
class HttpMethod
{
	
	/**
     * 错误响应
     *
     * @param string 错误信息
     * @param int    状态码
     *
     * @throws think\exception\HttpException
     *
     * @return void
     */
    public function error($message, $statusCode)
    {
        throw new ResponseException($statusCode, $message);
    }

	/**
     * 301 资源的URI已更改
     *
     * @param string $message
     *
     * @throws think\exception\HttpException
     *
     * @return void
     */
	public function movedPermanently($message = 'Moved Permanently')
	{
		$response = new Response($message);
        $response->setCode(301);
        return $response;
	}

	/**
     * 303 其他，如负载均衡
     *
     * @param string $message
     *
     * @throws think\exception\HttpException
     *
     * @return void
     */
	public function seeOther($message = 'See Other')
	{
		$response = new Response($message);
        $response->setCode(303);
        return $response;
	}
	
	/**
     * 400 请求错误响应
     *
     * @param string $message
     *
     * @throws think\exception\HttpException
     *
     * @return void
     */
    public function errorBadRequest($message = 'Bad Request')
    {
        $this->error($message, 400);
    }

    /**
     * 404 资源不存在错误响应
     *
     * @param string $message
     *
     * @throws think\exception\HttpException
     *
     * @return void
     */
    public function errorNotFound($message = 'Not Found')
    {
        $this->error($message, 404);
    }

    /**
     * 503 服务当前无法处理请求错误响应
     *
     * @param string $message
     *
     * @throws think\exception\HttpException
     *
     * @return void
     */
    public function errorUnavailable($message = 'Service Unavailable Error')
    {
        $this->error($message, 503);
    }

    /**
     * 500 通用错误响应
     *
     * @param string $message
     *
     * @throws think\exception\HttpException
     *
     * @return void
     */
    public function errorInternal($message = 'Internal Error')
    {
        $this->error($message, 500);
    } 




}