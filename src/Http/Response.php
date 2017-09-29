<?php 
namespace Zewail\Api\Http;

use think\response\Json as JsonResponse;
use Zewail\Api\Serializers\DataArraySerializer;
use Zewail\Api\Serializers\ArraySerializer;
use think\Config;

/**
 * @author   Chan Zewail <chanzewail@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/czewail/think-api
 */
class Response extends JsonResponse
{
    /**
     * 附加 meta 信息
     * 
     * @var array
     */
    private $meta = [];

    /**
     * 其他附加信息集合
     * 
     * @var array
     */
    private $adds = [];

    /**
     * 可用格式数组
     * @var array
     */
    private $serializers = [
        'DataArray' => DataArraySerializer::class,
        'Array' => ArraySerializer::class,
    ];

    /**
     * 默认格式
     * @var League\Fractal\Serializer\ArraySerializer::class
     */
    private $serializer = DataArraySerializer::class;

	/**
	 * [__construct]
	 * 
	 * @param string  $content
	 * @param integer $code   
	 * @param array   $header 
	 */
    function __construct($content = '', $code = 200, array $header = [])
    {
        $this->loadConfig();
        parent::__construct($content, $code, $header);
    }

    /**
     * 读取配置
     * @return [type] [description]
     */
    public function loadConfig()
    {
        if (Config::has('api')) {
            $config = Config::get('api');
            $this->serializer = in_array($config['serializer'], array_keys($this->serializers)) ? $this->serializers[$config['serializer']] : $this->serializers['DataArray'];
        }
    }

    /**
     * 处理数据
     * 
     * @param  $data
     * @return think\response\Json
     */
    protected function output($data)
    {
        $serializer = new $this->serializer($data, $this->meta, $this->adds);

        return parent::output($serializer->getData());
    }

    public function add($label, $value = '')
    {
        $this->adds[$label] = $value;
        return $this;
    }

    /**
     * 添加 Meta 信息
     * 
     * @param string $name  
     * @param string $value 
     */
    public function addMeta($name, $value = '')
    {
        $this->meta[$name] = $value;
        return $this;
    }

    /**
     * 批量设置 Meta 信息
     * 
     * @param array $meta  
     */
    public function setMeta(array $meta = [])
    {
        $this->meta = $meta;
        return $this;
    }

    /**
     * 设置 respone 的头部信息
     * 
     * @param [string] $name  
     * @param [mixed] $value
     */
    public function addHeader($name, $value = null) {
    	return $this->header($name, $value);
    }

    /**
     * 设置状态码
     * 
     * @param [number] $code
     */
    public function setCode($code)
    {
    	return $this->code($code);
    }

    /**
     * LastModified
     * 
     * @param [time] $time
     */
    public function setLastModified($time)
    {
    	return $this->setLastModified($time);
    }

    /**
     * ETag
     * 
     * @param [type] $eTag
     */
    public function setETag($eTag)
    {
    	return $this->eTag($eTag);
    }

    /**
     * expires
     * 
     * @param [type] $time
     */
    public function setExpires($time)
    {
    	return $this->expires($time);
    }

    /**
     * 页面缓存控制
     * @param string $cache
     * @return $this
     */
    public function setCacheControl($cache)
    {
        return $this->cacheControl($cache);
    }

    /**
     * 页面输出类型
     * 
     * @param string $contentType 
     * @param string $charset
     * @return $this
     */
    public function setContentType($contentType, $charset = 'utf-8')
    {
        return $this->contentType($contentType, $charset);
    }


}
