<?php 
namespace Zewail\Api\Response;

use Zewail\Api\Http\Response;
use Zewail\Api\Exceptions\TypeErrorException;
use think\exception\HttpException;
use think\Config;
use think\Model;
use think\model\Collection as ModelCollection;

class Factory
{

	protected $resources;

	function __construct()
	{
		$this->loadConfig();
	}

	/**
	 * 读取配置文件
	 */
	protected function loadConfig()
	{
		if (Config::has('resources')) {
            $this->resources = Config::get('resources');
        }
	}

	/**
     * 过滤单个模型
     * @param  [type] $item   [description]
     * @param  [type] $filter [description]
     * @return [type]         [description]
     */
   	protected function filterItem($item, $filter = null)
   	{
   		if (is_array($filter)) {
			return array_intersect_key($item->toArray(), $filter);
		} else if(is_string($filter)) {
			if (is_array($this->resources) && !empty($this->resources[$filter]) && array_key_exists($filter, $this->resources)) {
	        	return array_intersect_key($item->toArray(), array_flip($this->resources[$filter]));
        	}
		}
		return $item;
   	}
	

	/**
	 * 数组的响应
	 *
	 * @param  array
	 * @return Zewail\Api\Http\Response
	 */
	public function array($content = null)
	{
		return new Response($content);
	}


    /**
     * 单个模型的响应
     * 
     * @param  think\Model
     * @param  $filter
     * @return Zewail\Api\Http\Response    
     */
	public function item($item, $filter = null)
    {
    	// 判断是否是Model实例
    	if ($item instanceof Model) {
    		$response = $this->filterItem($item, $filter);
    	} else {
    		throw new TypeErrorException("this is not a Model instance", 1);
    	}
        
        return new Response($response);
    }

    /**
     * 多个模型的响应
     *
     * @param  think\Model
     * @return Zewail\Api\Http\Response
     */
    public function collection($collection, $filter = null)
    {
        if (is_array($collection)) {
        	$response = array_map(function($item) use ($filter) {
        		return $this->filterItem($item, $filter);
        	}, $collection);
        	return new Response($response);
        } else if ($collection instanceof ModelCollection) {
        	$response = [];
        	$collection->each(function($item) use ($filter, &$response) {
        		$response[] = $this->filterItem($item, $filter);
        	});
        	return new Response($response);
        }
        return new Response($collection);
    }

    /**
     * 分页模型的响应
     * 
     * @param  $collection 
     * @param  $filter
     * @return 
     */
    public function paginator($collection, $filter = null)
    {
    	if (is_array($collection->items())) {
    		$response = array_map(function($item) use ($filter) {
        		return $this->filterItem($item, $filter);
        	}, $collection->items());

    		try {
            	$total = $collection->total();
            	$last_page = $collection->lastPage();
	        } catch (\DomainException $e) {
	            $total = null;
	            $last_page = null;
	        }

	        $meta['paginator'] = [
	        	'total' => $total,
	        	'per_page' => $collection->listRows(),
	        	'current_page' => $collection->currentPage(),
	        	'last_page' => $last_page
	        ];

        	return (new Response($response))->setMeta($meta);
    	}
    	return new Response($collection);
    }

	/**
	 * 创建了资源的响应
	 *
	 * @param  资源响应位置
	 * @param  资源响应内容
	 * @return think\Response
	 */
	public function created($location = null, $content = null)
    {
    	$Response = new Response($content);
    	$Response->setCode(201);
    	if (! is_null($location)) {
            $Response->addHeader('Location', $location);
        }
        return $Response;
    }

    /**
     * 无内容响应
     *
     * @return think\Response
     */
    public function noContent()
    {
        $Response = new Response(null);

        return $Response->setCode(204);
    }

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
        // throw new HandleException($message, $statusCode);
        throw new HttpException($statusCode, $message);
    }

    /**
     * 404 错误响应
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
     * 400 错误响应
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
     * 403 错误响应
     *
     * @param string $message
     *
     * @throws think\exception\HttpException
     *
     * @return void
     */
    public function errorForbidden($message = 'Forbidden')
    {
        $this->error($message, 403);
    }

    /**
     * 500 错误响应
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

    /**
     * 401 错误响应
     *
     * @param string $message
     *
     * @throws think\exception\HttpException
     *
     * @return void
     */
    public function errorUnauthorized($message = 'Unauthorized')
    {
        $this->error($message, 401);
    }

    /**
     * 405 错误响应
     *
     * @param string $message
     *
     * @throws think\exception\HttpException
     *
     * @return void
     */
    public function errorMethodNotAllowed($message = 'Method Not Allowed')
    {
        $this->error($message, 405);
    }

}