<?php 
namespace Zewail\Api\Response\Method;

use Zewail\Api\Response\Method\HttpMethod;
use Zewail\Api\Http\Response;

/**
 * Patch 的响应
 *
 * @author   Chan Zewail <chanzewail@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/czewail/think-api
 */
class Patch extends HttpMethod
{
	/**
	 * 200 资源已更新
	 * 
	 * @param  string
	 * @return [type]
	 */
	public function updated($message = 'Updated')
	{
		$response = new Response($message);
        $response->setCode(200);
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