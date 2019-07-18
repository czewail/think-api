
[![Latest Stable Version](https://poser.pugx.org/zewail/think-api/v/stable)](https://packagist.org/packages/zewail/think-api)
[![Total Downloads](https://poser.pugx.org/zewail/think-api/downloads)](https://packagist.org/packages/zewail/think-api)
[![Latest Unstable Version](https://poser.pugx.org/zewail/think-api/v/unstable)](https://packagist.org/packages/zewail/think-api)
[![License](https://poser.pugx.org/zewail/think-api/license)](https://packagist.org/packages/zewail/think-api)


think-api 是给开发者提供的一套针对thinkphp的API扩展工具，帮助开发者方便快捷的建造自己的API应用。

**该包是针对thinkphp5.1以上版本**

## 简介

这个包提供了以下等工具：
- API版本管理
- 响应生成器
- 数据过滤器
- JWT(Json Web Token)的支持

`thinp-api` 是给开发者提供的一套API工具，帮助你方便快捷的建造你自己的API，有什么使用问题和反馈建议请在[issue](https://github.com/czewail/thinp-api/issues)中提出

另外，欢迎Star和Fork该项目😄😄😄😄😄


## 安装
安装该扩展包需要环境支持

- thinkphp 5.1+
- php 5.6+

修改你的 `composer.json` 文件，然后执行 `composer update` 把最后一个版本的包加入你的项目

```txt
"require": {
    "zewail/think-api": "1.1.*"
}
```

或者你可以在命令行执行 `composer require` 命令

```txt
composer require zewail/think-api:1.1.x
```

## 路由
如果你使用了自定义路由，则可以使用路由版本管理

##### 版本组

使用`think-api`的版本管理方法来创建版本

```php
$api = new \Zewail\Api\Routing\Router;

$api->version('v1', function () {
	// TODO 可以是thinkphp自带的路由
});
```

或者使用门面（Facede）

```php
use Zewail\Api\Facades\ApiRoute;

ApiRoute::version('v1', function(){
    // TODO 可以是thinkphp自带的路由
});
```

你想一个分组返回多个版本，只需要传递一个版本数组

```php
ApiRoute::version(['v1', 'v2'], function () {
	// TODO 可以是thinkphp自带的路由
});
```

##### 创建路由

```php
ApiRoute::version('v1', function(){
    Route::rule('new/:id','index/News/read');
});
```

因为每个版本分组了，你可以为相同 URL 上的同一个路由创建不同响应

```php
ApiRoute::version('v1', function () {
	Route::rule('new/:id','app\index\controller\V2\News@read');
  	// 或者
  	Route::rule('new/:id','index/V2.News/read');
});

ApiRoute::version('v2', function () {
    Route::rule('new/:id','app\index\controller\V2\News@read');
});
```

##### 访问特定路由版本

默认访问配置文件中的默认版本

但是，我们可以在Http的头信息中附带`Api-Version`参数，或者直接在在url或body中附带`version`参数来访问指定版本

```txt
http://example.com/new/102?version=v2
```
## 响应
#### 响应生成器

响应生成器提供了一个流畅的接口去方便的建立一个定制化的响应

要利用响应生成器, 你的控制器需要使用`Zewail\Api\Api` trait, 可以建立一个通用控制器，然后你的所有的 API 控制器都继承它。

```php
namespace app\index\controller;

use think\Controller;
use Zewail\Api\Api;

class BaseController extends Controller
{
	use Api;
}
```

然后你的控制器可以直接继承基础控制器。响应生成器可以在控制器里通过 `$response` 属性获取。

当然，也可以使用门面(Facade)来获取

```php
namespace app\index\controller;

use Zewail\Api\Facades\Response as ApiResponse;

class IndexController
{
	public function index() {
        return ApiResponse::array([]);
    }
}
```
##### 简单响应

```php
// 简单的成功响应, 默认200状态码, 可以在第二个参数改变
// 使用 trait, 其他方法都可以使用该方法，下面都使用 Facade 演示
return $this->response->array($user->toArray());
// 使用 Facade
return ApiResponse::success('Success', 200);
```

##### 响应一个数组

```php
$user = User::get($id);
return ApiResponse::array($user->toArray());
```

##### 响应一个元素

```php
$user = User::get($id);
return ApiResponse::item($user);
```

##### 响应一个元素集合

```php
$users = User::all();
return ApiResponse::collection($users);
```

##### 分页响应

```php
$users = User::paginate(10);
return ApiResponse::paginator($users);
```
#### 使用别名生成响应

捕获错误响应需要接管系统的异常处理机制，将系统`config/app.php`中的 `exception_handle`配置为`Zewail\Api\Exceptions\handleException`

```php
// 异常处理handle类 留空使用 \think\exception\Handle
'exception_handle'       => 'Zewail\Api\Exceptions\handleException',
```

返回一个错误响应

```php
return ApiResponse::BadRequest();
```

当然也可以返回成功的响应

```php
return ApiResponse::OK($data);
```

| 方法名                        | 状态码 | 说明                            |
| ----------------------------- | ------ | ------------------------------- |
| Continue                      | 100    | Continue                        |
| SwitchingProtocols            | 101    | Switching Protocols             |
| Processing                    | 102    | Processing                      |
| EarlyHints                    | 103    | Early Hints                     |
| OK                            | 200    | OK                              |
| Created                       | 201    | Created                         |
| Accepted                      | 202    | Accepted                        |
| NonAuthoritativeInformation   | 203    | Non-Authoritative Information   |
| NoContent                     | 204    | No Content                      |
| ResetContent                  | 205    | Reset Content                   |
| PartialContent                | 206    | Partial Content                 |
| MultiStatus                   | 207    | Multi-Status                    |
| AlreadyReported               | 208    | Already Reported                |
| IMUsed                        | 226    | IM Used                         |
| MultipleChoices               | 300    | Multiple Choices                |
| MovedPermanently              | 301    | Moved Permanently               |
| Found                         | 302    | Found                           |
| SeeOther                      | 303    | See Other                       |
| NotModified                   | 304    | Not Modified                    |
| UseProxy                      | 305    | Use Proxy                       |
| TemporaryRedirect             | 307    | Temporary Redirect              |
| PermanentRedirect             | 308    | Permanent Redirect              |
| BadRequest                    | 400    | Bad Request                     |
| Unauthorized                  | 401    | Unauthorized                    |
| PaymentRequired               | 402    | Payment Required                |
| Forbidden                     | 403    | Forbidden                       |
| NotFound                      | 404    | Not Found                       |
| MethodNotAllowed              | 405    | Method Not Allowed              |
| NotAcceptable                 | 406    | Not Acceptable                  |
| ProxyAuthenticationRequired   | 407    | Proxy Authentication Required   |
| RequestTimeou                 | 408    | Request Timeou                  |
| Conflict                      | 409    | Conflict                        |
| Gone                          | 410    | Gone                            |
| LengthRequired                | 411    | Length Required                 |
| PreconditionFailed            | 412    | Precondition Failed             |
| PayloadTooLarge               | 413    | Payload Too Large               |
| URITooLong                    | 414    | URI Too Long                    |
| UnsupportedMediaType          | 415    | Unsupported Media Type          |
| RangeNotSatisfiable           | 416    | Range Not Satisfiable           |
| ExpectationFailed             | 417    | Expectation Failed              |
| IAmATeapot                    | 418    | I\'m a teapot                   |
| MisdirectedRequest            | 421    | Misdirected Request             |
| UnprocessableEntity           | 422    | Unprocessable Entity            |
| Locked                        | 423    | Locked                          |
| FailedDependency              | 424    | Failed Dependency               |
| UnorderedCollection           | 425    | Unordered Collection            |
| UpgradeRequired               | 426    | Upgrade Required                |
| PreconditionRequired          | 428    | Precondition Required           |
| TooManyRequests               | 429    | Too Many Requests               |
| RequestHeaderFieldsTooLarge   | 431    | Request Header Fields Too Large |
| UnavailableForLegalReasons    | 451    | Unavailable For Legal Reasons   |
| InternalServerError           | 500    | Internal Server Error           |
| NotImplemented                | 501    | Not Implemented                 |
| BadGateway                    | 502    | Bad Gateway                     |
| ServiceUnavailable            | 503    | Service Unavailable             |
| GatewayTimeout                | 504    | Gateway Timeout                 |
| HTTPVersionNotSupported       | 505    | HTTP Version Not Supported      |
| VariantAlsoNegotiates         | 506    | Variant Also Negotiates         |
| InsufficientStorage           | 507    | Insufficient Storage            |
| LoopDetected                  | 508    | Loop Detected                   |
| NotExtended                   | 510    | Not Extended                    |
| NetworkAuthenticationRequired | 511    | Network Authentication Required |



#### 添加其他响应数据

##### 添加 Meta 数据

```php
return ApiResponse::item($user)->addMeta('foo', 'bar');
```

或者直接设置 Meta 数据的数组

```php
return ApiResponse::item($user)->setMeta($meta);
```

##### 设置响应状态码

```php
return ApiResponse::item($user)->setCode(200);
```

##### 添加额外的头信息

```php
// 提供两种设置方式
return ApiResponse::item($user)->addHeader('X-Foo', 'Bar');
return ApiResponse::item($user)->addHeader(['X-Foo' => 'Bar']);
```

##### 设置 LastModified

```php
return ApiResponse::item($user)->setLastModified($time);
```

##### 设置 ETag

```php
return ApiResponse::item($user)->setETag($eTag);
```

##### 设置 Expires

```php
return ApiResponse::item($user)->setExpires($time);
```

##### 页面缓存控制

```php
return ApiResponse::item($user)->setCacheControl($cache);
```

### 响应数据过滤

其中`item` `collection`  `paginator`具有两个参数，第一个参数为模型数据，第二个参数为数据过滤列表

```php
// 如查询出来的$user具有id, name, age, mobile等属性
// 在设置了第二个参数为['id', 'name', 'age']后，将会过滤其他属性，只返回给接口列出的属性
return ApiResponse::item($user, ['id', 'name', 'age']);
return ApiResponse::collection($users, ['id', 'name', 'age']);
return ApiResponse::paginator($users, ['id', 'name', 'age']);
```

或者通过`only`与`except`方法过滤数据

```php
// 只选择模型中的id、name、age属性
return ApiResponse::only(['id', 'name', 'age'])->item($user);
// 排除模型属性age
return ApiResponse::except(['age'])->item($user);
// 还可以一起使用, 选择id、name、age属性后排除age
return ApiResponse::only(['id', 'name', 'age'])->except(['age'])->item($user);
```

##### 集中管理

提供了一个配置文件用于数据过滤或者说是数据资源的集中管理

使用该功能需要在thinkphp中新建一个配置文件`resources.php`

```php
return [
  // 用户相关接口
  // 例如设置一些用户的相关接口资源
  'user.age' => ['id', 'name', 'age'],
  'user.mobile' => ['id', 'name', 'mobile'],
];
```

然后在返回接口数据的时候在`item` `collection`  `paginator`第二个参数传入该标识即可

```php
// 返回{'data': {'id':1, 'name': 'xiaoming', 'age': 20}}
return ApiResponse::item($user, 'user.age');
// 返回{'data': {'id':1, 'name': 'xiaoming', 'mobile': '13777777777'}}
return ApiResponse::item($user, 'user.mobile');
```

或者通过`only`与`except`方法

```php
// 返回{'data': {'id':1, 'name': 'xiaoming', 'age': 20}}
return ApiResponse::only('user.age')->item($user);
// 返回{'data': {'id':1, 'name': 'xiaoming', 'mobile': '13777777777'}}
return ApiResponse::only('user.mobile')->item($user);
```

> item、collection、paginator的第二个过滤参数属性，会覆盖only与except方法

#### 设置serializer

如果默认配置`Array`，想返回`DataArray`格式的数据，可以：

```php
// 返回Array格式的数据
return ApiResponse::item($user)->serializer('Array');
// 返回DataArray格式的数据
return ApiResponse::item($user)->serializer('DataArray');
```

## JWT
JWT相关知识大家百度一下吧，网上很多，直接上代码

### 创建Token

使用 JWT 门面的 attempt 方法来自动验证

```php
namespace app\index\controller;

use app\index\model\User;
use Zewail\Api\Facades\Response;
use Zewail\Api\Facades\JWT;
use Zewail\Api\Exceptions\JWTException;

class Authenticate
{
	public function authenticate()
	{
        // $credentials 可以从请求中获取
        $credentials = ['email'=>'chanzewail@gmail.com', 'password' => '123456'];
        $token = JWT::attempt($credentials);
	}
}
```

这里使用了 email 和 password 来验证用户是否合法，如果你的用户是通过 mobile或其它字段作为标识，那么可以在 app\index\model\User 模型中，添加 jwtSub 字段：

```php
namespace app\index\model;

use think\Model;

class User extends Model
{
	public $jwtSub = 'mobile';
}
```

当然，如果你的密码 不是用的 password （绝大多数都用这个，不排除少数奇怪的命名….）,那么你可以添加 jwtPassword 字段：

```php
public $jwtPassword = 'strange_password';
```

这里验证psssword默认使用md5加密，绝大多数情况下这是不够安全的，很多都有自定义的加密方式，那么还有验证密码的方法，添加：

```php
public function jwtEncrypt($password)
{
 	// 只要返回你加密后的结果，会自动比对数据库字段
  	return md5(sha1($password));
}
```

还可以直接通过用户对象实例创建token

```php
$user = User::get(1);
$token = JWT::fromUser($user);
```

还可以自定义 Payload 创建任意数据

```php
$customClaims = ['foo' => 'bar', 'baz' => 'bob'];
$payload = JWT::makePayload($customClaims);
$token = JWT::encode($payload);
```

### 用户认证

要通过http发送一个需要认证通过的请求，需要设置Authorization头

```php
Authorization: Bearer {token}
```

或者将token信息包含到URL中

```php
http://api.example.com/news?token={token}
```

#####  解析token

`resolveToken`方法可以将token还原为payload数组

```php
$payload = JWT::resolveToken();
// ['foo' => 'bar', 'baz' => 'bob']
```

如果是从user模型创建的token，那么还可以使用authenticate方法，直接验证用户，成功后返回用户模型，失败返回false

```php
if ($user = JWT::authenticate()) {
    // todo
}
```

当然还有更加手动的方法

```php
// 从请求中获取token
$token = JWT::getToken();

// todo

// 对token进行解码
$payload = JWT::decode($token);
```


## 配置

该扩展包共有3个配置文件
> 配置文件仅支持全局配置目录使用

- `api.php`：管理接口配置
- `resources.php`：过滤管理器配置
- `jwt.php`：JWT相关配置

配置文件可以在`vendor/zewail/think-api/config`目录下找到，也可以手动创建它们

### api.php

```php
retrun [
    //配置项
];
```

##### version

api的默认版本

##### serializer

api返回的数据格式,可选：

- `DataArray`：数组带data格式，默认值

  ```txt
  // item
  {
    'data': [...],
    'meta': [...],
    ...
  }

  // collection
  {
    'data': [
        {...},
        {...},
        {...},
      ],
    'meta': [...],
    ...
  }
  ```

- `Array`：数组格式

  ```txt
  // item
  {
    'item_field1': '...',
    'item_field2': '...',
    'meta': [...],
    ...
  }

  // collection
  {
    [
    	{...},
      {...},
    ],
    'meta': [...],
    ...
  } 
  ```

  ​

### resources.php

```php
retrun [
  //配置项
  // 用户相关接口
  // 例如设置一些用户的相关接口资源
  'user.age' => ['id', 'name', 'age'],
  'user.mobile' => ['id', 'name', 'mobile'],
];
```

该配置文件用于数据过滤管理，在返回接口数据的时候在`item` `collection`  `paginator`第二个参数传入该标识来使用

```php
// 返回{'data': {'id':1, 'name': 'xiaoming', 'age': 20}}
return $this->response->item($user, 'user.age');
// 返回{'data': {'id':1, 'name': 'xiaoming', 'mobile': '13777777777'}}
return $this->response->item($user, 'user.mobile');
```



### jwt.php

```php
retrun [
    //配置项
];
```

##### ttl

token的过期时间， 默认为120分钟，单位分钟

##### deviation

允许误差时间，默认为60秒，单位秒

##### algorithm

加密算法，支持：

- `HS256`: `HMAC 使用 SHA-256 算法加密`
- `HS512`: `HMAC 使用 SHA-512 算法加密`
- `RS256`: `RSA 使用 SHA-256 算法加密`

##### key

如果使用了HMAC加密方式，则需要配置该项，为自定义字符串

##### privateKeyPath

如果使用了RSA加密方式，则需要配置该项，为`.pem`结尾的私钥文件路径

##### publicKeyPath

如果使用了RSA加密方式，则需要配置该项，为`.pem`结尾的公钥文件路径

##### user

如果需要使用用户操作相关方法，则需定义该项，为用户模型所在路径，如

```php
'user' => app\index\model\User::class,
```



## 授权协议

[MIT license](LICENSE)


## CHANGELOG
#### 1.1.0-beta1
- 修复 php7 以下环境调用`response::array`报错的问题
- 添加全新状态码别名方法调用
- 移除动词方法别名调用生成Http异常（现在统一使用状态码别名）
