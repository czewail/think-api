<?php 
namespace Zewail\Api\Exceptions;

use think\exception\Handle;
use Zewail\Api\Http\Response;
use think\exception\HttpException;
use think\Config;
use think\App;
use Exception;

class handleHttpException extends Handle
{
	// 默认错误信息格式
	protected $error_format = [
		'message' => ':message',
        'errors' => ':errors',
        'code' => ':code',
        'status_code' => ':status_code',
	];
	// 默认关闭debug模式
	protected $debug = false;

	/**
	 * [__construct]
	 */
	function __construct()
	{
		$this->loadConfig();
	}

	/**
	 * 加载相关配置
	 */
	protected function loadConfig() {
		if (Config::has('api.error_format')) {
            $this->error_format = Config::get('api.error_format');
        }
		if (Config::has('api.debug')) {
            $this->debug = Config::get('api.debug');
        }
	}
	/**
	 * 获取debug信息
	 * @param  Exception $e [description]
	 * @return [type]       [description]
	 */
	protected function debugInfomation(Exception $e)
	{
		// debug 信息
		if (App::$debug) {
    		$debug = [
                'name'    => get_class($e),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'message' => $this->getMessage($e),
                'trace'   => $e->getTrace(),
                'code'    => $this->getCode($e),
                'source'  => $this->getSourceCode($e),
                'datas'   => $this->getExtendData($e),
                'tables'  => [
                    'GET Data'              => $_GET,
                    'POST Data'             => $_POST,
                    'Files'                 => $_FILES,
                    'Cookies'               => $_COOKIE,
                    'Session'               => isset($_SESSION) ? $_SESSION : [],
                    'Server/Request Data'   => $_SERVER,
                    'Environment Variables' => $_ENV,
                ],
            ];
    	} else {
            // 部署模式仅显示 Code 和 Message
            $debug = [
                'code'    => $this->getCode($e),
                'message' => $this->getMessage($e),
            ];
            if (!Config::get('show_error_msg')) {
                // 不显示详细错误信息
                $debug['message'] = Config::get('error_message');
            }
        }
	    return $debug;
	}

	/**
	 * render
	 */
    public function render(Exception $e) 
    {
        // 请求异常
        if ($e instanceof HttpException) {
        	// 将错误格式中的变量标识替换为内容
        	$response = array_map(function($item) use ($e) {
        		$item = str_replace(':message', $e->getMessage(), $item);
        		$item = str_replace(':errors', '', $item);
        		$item = str_replace(':code', $e->getStatusCode(), $item);
        		$item = str_replace(':status_code', $e->getStatusCode(), $item);
        		return $item;
        	}, $this->error_format);

        	// debug 信息
        	if ($this->debug) {
        		$debug = $this->debugInfomation($e);
		        // 返回中加入debug信息
		        return (new Response($response, $e->getStatusCode()))->add('debug', $debug);
        	}
        	return (new Response($response, $e->getStatusCode()));
        }

        // 其他错误交给系统处理
        return parent::render($e);
    }	
}