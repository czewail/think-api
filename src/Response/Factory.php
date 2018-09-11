<?php 
namespace Zewail\Api\Response;

use Zewail\Api\Http\Response;
use Zewail\Api\Setting\Set;
use Zewail\Api\Exceptions\TypeErrorException;
use Zewail\Api\Exceptions\ResponseException;

use Config;
use ErrorException;
use think\Model;
use think\model\Collection as ModelCollection;


/**
 * @author   Chan Zewail <chanzewail@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/czewail/think-api
 */
class Factory extends Status
{
//    use Status {
//        Status::__construct as private __StatusConstruct;
//    }
    // 过滤器配置
    protected $resources = [];

    // 需要保留的字段
    protected $only = null;

    // 需要排除的字段
    protected $except = null;

    // 构造方法
    public function __construct()
    {
//        $this->__StatusConstruct();
        parent::__construct();
        Set::resources(function($config) {
            $this->resources = $config;
        });
    }

    /**
     * 过滤单个模型
     * @param  [type] $item   [description]
     * @param  [type] $filter [description]
     * @return [type]         [description]
     */
    protected function filterItem($item, $filter = null)
    {
        // 模型数组
        $item_array = $item->toArray();
        // 过滤的交集, 默认所有
        $result = $item_array;
        // 存在方法内过滤参数，覆盖only与except方法
        if ($filter) {
            if (is_array($filter)) {
                $result = array_intersect_key($item_array, array_flip($filter));
            } else if(is_string($filter)) {
                if (is_array($this->resources) && !empty($this->resources[$filter]) && array_key_exists($filter, $this->resources)) {
                    $result = array_intersect_key($item_array, array_flip($this->resources[$filter]));
                }
            }
        } else {
            if (is_array($this->only)) {
                $result = array_intersect_key($item_array, array_flip($this->only));
            } else if (is_string($this->only)) {
                if (is_array($this->resources) && !empty($this->resources[$this->only]) && array_key_exists($this->only, $this->resources)) {
                    $result = array_intersect_key($item_array, array_flip($this->resources[$this->only]));
                }
            }
            if (is_array($this->except)) {
                $result = array_diff_key($result, array_flip($this->except));
            } else if (is_string($this->except)) {
                if (is_array($this->resources) && !empty($this->resources[$this->except]) && array_key_exists($this->except, $this->resources)) {
                    $result = array_diff_key($result, array_flip($this->resources[$this->except]));
                }
            }
        }
        return $result;
   	}

    /**
     * 设置需要保留的字段
     *
     * @param  array
     * @return Zewail\Api\Http\Response
     */
    public function only($filter = null)
    {
        $this->only = $filter;
        return $this;
    }

    /**
     * 设置需要排除的字段
     *
     * @param  array
     * @return Zewail\Api\Http\Response
     */
    public function except($filter = null)
    {
        $this->except = $filter;
        return $this;
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
        $Response->setCode(204);
        return $Response;
    }

    /**
     * 301 资源的URI已更改
     *
     * @param string $message
     *
     * @return think\Response
     */
    public function movedPermanently($message = 'Moved Permanently')
    {
        $Response = new Response($message);
        $Response->setCode(301);
        return $Response;
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
        throw new ResponseException($statusCode, $message);
    }

    /**
     * 成功响应
     *
     * @param string 成功信息
     * @param int    状态码
     *
     * @throws think\exception\HttpException
     *
     * @return void
     */
    public function success($message = null, $statusCode = 200)
    {
        $response = new Response($message);
        $response->setCode($statusCode);
        return $response;
    }

    /**
     * Call magic methods beginning with "with".
     *
     * @param string $method
     * @param array  $parameters
     *
     * @throws \ErrorException
     *
     * @return mixed
     */
    public function __call($method, array $parameters)
    {
        if ($method == 'array') {
            return new Response($parameters[0]);
        } else if (array_key_exists($method, $this->methods)) {
            return call_user_func_array('parent::__call', [$method, $parameters]);
        }
        throw new ErrorException('Undefined method '.get_class($this).'::'.$method);
    }
}