<?php 
namespace Zewail\Api\Response\Method;

use Zewail\Api\Response\Method\HttpMethod;
use Zewail\Api\Http\Response;

/**
 * Delete 的响应
 *
 * @author   Chan Zewail <chanzewail@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/czewail/think-api
 */
class Delete extends HttpMethod
{
	/**
	 * 200 资源已删除
	 * 
	 * @param  string
	 * @return [type]
	 */
	public function deleted($message = 'Deleted')
	{
		$response = new Response($message);
        $response->setCode(200);
        return $response;
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