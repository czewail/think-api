<?php 
namespace Zewail\Api\JWT\Factories;

use Zewail\Api\JWT\Factories\Claims\Audience;
use Zewail\Api\JWT\Factories\Claims\Expiration;
use Zewail\Api\JWT\Factories\Claims\IssuedAt;
use Zewail\Api\JWT\Factories\Claims\Issuer;
use Zewail\Api\JWT\Factories\Claims\JwtId;
use Zewail\Api\JWT\Factories\Claims\NotBefore;
use Zewail\Api\JWT\Factories\Claims\Subject;
use think\Container;
use DateTime;
use DateInterval;

class Payload
{
    protected $request;

    protected $ttl = 120;

    private $classMap = [
        'aud' => Audience::class,
        'exp' => Expiration::class,
        'iat' => IssuedAt::class,
        'iss' => Issuer::class,
        'jti' => JwtId::class,
        'nbf' => NotBefore::class,
        'sub' => Subject::class,
    ];

    public function __construct($config)
    {
        if (isset($config['ttl']) && $config['ttl'] >= 0) {
            $this->setTTL($config['ttl']);
        }
        $this->request = Container::get('request');
    }

    /**
     * 是否存在
     * @param  [type]  $name [description]
     * @return boolean       [description]
     */
    public function has($name)
    {
        return array_key_exists($name, $this->classMap);
    }

    /**
     * 通过名称调用方法创建对象
     * @param  [type] $name [description]
     * @return [type]       [description]
     */
    public function make($name)
    {
        if ($this->has($name)) {
            return new $this->classMap[$name]($this->$name());
        }
    }

    /**
     * Issuer
     * 
     * @return url
     */
    public function iss()
    {
        return $this->request->domain() . $this->request->baseUrl();
    }

    /**
     * IssuedAt
     * 
     * @return Timestamp
     */
    public function iat()
    {
        $datetime = new DateTime;
        // return $datetime->format('Y-m-d H:i:s');
        return $datetime->getTimestamp();
    }

    /**
     * Expiration
     * 
     * @return Timestamp
     */
    public function exp()
    {
        $datetime = new DateTime;
        $datetime->add(new DateInterval('PT' . $this->ttl . 'M'));
        return $datetime->getTimestamp();
    }

    /**
     * NotBefore
     * 
     * @return Timestamp
     */
    public function nbf()
    {
        $datetime = new DateTime;
        return $datetime->getTimestamp();
    }

    /**
     * JwtId
     * 
     * @return string
     */
    public function jti()
    {
        $length = 16;
        $string = '';
        while (($len = strlen($string)) < $length) {
            $size = $length - $len;
            $bytes = random_bytes($size);
            $string .= substr(str_replace(['/', '+', '='], '', base64_encode($bytes)), 0, $size);
        }
        return $string;
    }

    public function setTTL($ttl)
    {
        $this->ttl = $ttl;

        return $this;
    }

    public function getTTL()
    {
        return $this->ttl;
    }


}