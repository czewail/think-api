<?php
namespace Zewail\Api\JWT;

use Config;
use Request;
use think\Model;
use Zewail\Api\Exceptions\JWTException;
use Zewail\Api\Exceptions\UnauthenticateException;
use Zewail\Api\Setting\Set;
use Zewail\Api\JWT\Factories\Code;
use Zewail\Api\JWT\Factories\Payload as PayloadFactory;
use Zewail\Api\JWT\Factories\Claims\Collection;
use Zewail\Api\JWT\Factories\Claims\Subject;
use Zewail\Api\JWT\Factories\Claims\Custom;

/**
 * @author   Chan Zewail <chanzewail@gmail.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://github.com/czewail/think-api
 */
class Factory
{

    protected $PayloadFactory;

    protected $defaultClaims = [
        'iss',
        'iat',
        'exp',
        'nbf',
        'jti',
    ];

    protected $claims;

    protected $config = [];


    function __construct()
    {
        Set::jwt(function($config) {
            $this->config = $config;
        });
        $this->PayloadFactory = new PayloadFactory($this->config);
        $this->claims = new Collection;
    }

    /**
     * 验证账号
     * @param  array  $credentials [description]
     * @return [type]              [description]
     */
    public function attempt(array $credentials, array $customClaims = [])
    {
        $userModel = new $this->config['user'];

        // jwtSub属性不存在则使用email
        $subField = $this->getModelSub($userModel);
        $pwdField = $this->getModelPwd($userModel);
        // 查询模型
        $user = $userModel->where($subField, $credentials[$subField])->find();
        if ($user) {
            // 获取加密后的密码
            if (method_exists($userModel, 'jwtEncrypt')) {
                $inputPwd = $userModel->jwtEncrypt($credentials[$pwdField], $user);
            } else {
                $inputPwd = md5($credentials[$pwdField]);
            }
            // 验证密码
            if ($inputPwd !== $user->$pwdField) {
                throw new UnauthenticateException('账号验证失败');
            }
            return $this->fromSubject(new Subject($user->$subField, $customClaims));
        } else {
            throw new UnauthenticateException('账号不存在');
        }
    }

     /**
      * 从已认证的用户创建token
      * @param  Model  $user [description]
      * @return [type]       [description]
      */
    public function fromUser(Model $user, array $customClaims = [])
    {
        // jwtSub属性不存在则使用email
        $subField = isset($user->jwtSub) ? $user->jwtSub : 'email';
        return $this->fromSubject(new Subject($user->$subField), $customClaims);
    }

    /**
     * 将payload加密成为token
     * @param  Payload $payload [description]
     * @return [type]           [description]
     */
    public function encode(Payload $payload)
    {
        $code = new Code;
        return $code->encode($payload->toArray());
    }
    /**
     * 将payload加密成为token
     * @param  Payload $payload [description]
     * @return [type]           [description]
     */
    public function decode($token)
    {
        $code = new Code;
        return (array) $code->decode($token);
    }

    /**
     * 创建payload对象
     * @param  array  $customClaims [description]
     * @return [type]               [description]
     */
    public function makePayload(array $customClaims = [])
    {
        foreach ($customClaims as $key => $custom) {
            $paload = new Custom($key, $custom);
            $this->claims->unshift($paload->getValue(), $paload->getName());
        }
        return new Payload($this->claims);
    }

    /**
     * 解析token
     * @return [type] [description]
     */
    public function resolveToken()
    {
        $code = new Code;
        if ($token = $this->getToken()) {
            $payload = $code->decode($token);
            return (array) $payload;
        }
        return false;
    }

    /**
     * 验证并返回用户模型
     * @return [type] [description]
     */
    public function authenticate()
    {
        $payload = $this->resolveToken();
        if ($payload && isset($payload['sub'])) {
            $userModel = new $this->config['user'];
            // jwtSub属性不存在则使用email
            $subField = $this->getModelSub($userModel);
            // 查询模型
            $user = $userModel->where($subField, $payload['sub'])->find();
            return $user;
        }
        return false;
    }

    /**
     * 从请求中获取token
     * @return [type] [description]
     */
    public function getToken()
    {
        if ($Authorization = Request::header('Authorization')) {
            $authArr = explode(' ', $Authorization);
            if ( isset($authArr[0]) && $authArr[0] === 'Bearer') {
                if (isset($authArr[1])) {
                    return $authArr[1];
                }
            }
        } else if (Request::has('token')) {
            return Request::get('token');
        }
        return false;
    }

    /**
     * 获取sub字段
     * @return [type] [description]
     */
    protected function getModelSub(Model $userModel)
    {
        return isset($userModel->jwtSub) ? $userModel->jwtSub : 'email';
    }

    /**
     * 获取pwd字段
     * @return [type] [description]
     */
    protected function getModelPwd(Model $userModel)
    {
        return isset($userModel->jwtPassword) ? $userModel->jwtPassword : 'password';
    }


    /**
     * 增加sub并构建Claims
     * @param  Subject $sub [description]
     * @return [type]       [description]
     */
    protected function fromSubject(Subject $sub, array $customClaims = [])
    {
        $this->buildDefaultClaims($customClaims);
        $this->claims->unshift($sub->getValue(), $sub->getName());
        return $this->encode(new Payload($this->claims));
    }


    /**
     * 构建默认Claims
     * @return [type] [description]
     */
    private function buildDefaultClaims(array $customClaims = [])
    {
        // 如果过期时间未设置则删除过期时间
        if ($this->PayloadFactory->getTTL() === null && $key = array_search('exp', $this->defaultClaims)) {
            unset($this->defaultClaims[$key]);
        }
        // 遍历默认输入
        foreach ($this->defaultClaims as $claim) {
            // $this->claims[$claim] = $this->PayloadFactory->make($claim);
            $paload = $this->PayloadFactory->make($claim);
            $this->claims->unshift($paload->getValue(), $paload->getName());
        }
        // 遍历自定义输出
        foreach ($customClaims as $key => $custom) {
            $paload = new Custom($key, $custom);
            $this->claims->unshift($paload->getValue(), $paload->getName());
        }
        return $this;
    }
}


