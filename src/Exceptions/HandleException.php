<?php 
namespace Zewail\Api\Exceptions;

use think\exception\Handle;
use Zewail\Api\Http\Response;
use Exception;

/**
* @author   Chan Zewail <chanzewail@gmail.com>
* @license  https://opensource.org/licenses/MIT MIT
* @link     https://github.com/czewail/think-api
*/
class HandleException extends Handle
{
    /**
    * render
    */
    public function render(Exception $e) 
    {
        // Token 授权失败的异常
        if ($e instanceof TokenExpiredException || $e instanceof TokenInvalidException || $e instanceof UnauthenticateException) {
            return new Response(['message' => $e->getMessage(), 'status_code' => 401], 401);
        }

        if ($e instanceof JWTException) {
            return new Response(['message' => $e->getMessage(), 'status_code' => 500], 500);
        }

        // http状态码异常
        if ($e instanceof ResponseException) {
            return new Response(['message' => $e->getMessage(), 'status_code' => $e->getStatusCode()], $e->getStatusCode());
        }
        // 其他错误交给系统处理
        return parent::render($e);
    }	
}