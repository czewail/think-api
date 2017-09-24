# think-api
帮助thinkphp 5 开发者快速、轻松的构建Api

### 说明

该包处于开发状态中，已列出的功能均可使用

有什么建议或者反馈可以在[issue](https://github.com/czewail/thinp-api/issues)中提出

另外，欢迎Star和Fork该项目😄😄😄😄😄

### 功能

- 响应生成器可以快速生成格式化、定制化的接口数据
- 过滤器可以使接口数据集中管理
- 转换器规范数据格式，并提供多种数据格式
- JWT支持，接口授权简单方便
- 正在增加其它功能..........



## 开始

#### 环境要求

- PHP >= 5.6.0
- ThinkPHP >= 5.0

#### 安装

你需要修改你的 `composer.json` 文件，然后执行 `composer update` 把最后一个版本的包加入你的项目

```txt
"require": {
    "zewail/think-api": "0.1.*@dev"
}
```

或者你可以在命令行执行 `composer require` 命令

```bash
composer require zewail/think-api:0.1.x@dev
```

## 配置

`think-api` 提供了两个配置文件`api.php`和`resources.php`

`api.php`用于常用配置项, 默认配置如下:

```php
return [
	// 可选 DataArray, Array
	'serializer' => 'DataArray',

	// 开启接口调试模式，接口数据会带上debug字段，包含thinkphp的所有异常信息
	'debug' => false,

	// 错误信息返回格式
	'error_format' => [
		'message' => ':message',
        'errors' => ':errors',
        'code' => ':code',
        'status_code' => ':status_code',
	],

	/*****************************/
	/*--------JWT 配置------------*/
	/*****************************/
	// 是否开启jwt功能
	'jwt' => true,

	// jwt 加密算法
	// 可选 HS256 HS512 RS256
	'jwt_algorithm' => 'HS256',
  
	// HMAC加密须配置
  	// 加密密钥，自定义字符串
	'jwt_key' => 'ex-key',

	// openssl加密须配置
	// 私钥路径，必须绝对路径
	'jwt_privateKeyPath' => '/www/keys/rsa_private_key.pem',

	// openssl加密须配置
	// 公钥路径，必须绝对路径
	'jwt_publicKeyPath' => '/www/keys/rsa_public_key.pem',

	// 允许误差时间（单位秒）
	'jwt_deviation' => 0,
];
```

在全局配置目录或者模块配置目录（取决于你需要将该功能用于全局还是该模块）新建`api.php`并将以上内容复制进去

`resources.php`用于数据过滤集中管理,见[数据过滤集中管理](#集中管理)

## 响应

#### 响应生成器

响应生成器提供了一个流畅的接口去方便的建立一个定制化的响应

要利用响应生成器, 你的控制器需要使用`Zewail\Api\Api` trait, 可以建立一个通用控制器，然后你的所有的 API 控制器都继承它。

```php
<?php
namespace app\index\controller;

use think\Controller;
use Zewail\Api\Api;

class BaseController extends Controller
{
	use Api;
}

```

然后你的控制器可以直接继承基础控制器。响应生成器可以在控制器里通过 `$response` 属性获取。

##### 响应一个数组

```php
$user = User::get($id);
return $this->response->array($user->toArray());
```

##### 响应一个元素

```php
$user = User::get($id);
return $this->response->item($user);
```

##### 响应一个元素集合

```php
$users = User::all();
return $this->response->collection($users);
```

##### 分页响应

```php
$users = User::paginate(10);
return $this->response->paginator($users);
```

##### 无内容响应

```php
return $this->response->noContent();
```

##### 创建资源响应

```php
// 返回201状态码，可传入资源位置信息
return $this->response->created($location);
```

##### 错误响应

内置了一些常用错误

```php
// 自定义消息和状态码的普通错误
return $this->response->error('错误信息', 404);

// bad request 错误, 状态码为400
// 该方法可以传递一个参数，为该错误的自定义消息
return $this->response->errorBadRequest();

// 未认证错误, 状态码为401
// 该方法可以传递一个参数，为该错误的自定义消息
return $this->response->errorUnauthorized();

// 服务器拒绝错误, 状态码为403
// 该方法可以传递一个参数，为该错误的自定义消息
return $this->response->errorForbidden();

// 没有找到资源的错误, 状态码为404
// 该方法可以传递一个参数，为该错误的自定义消息
return $this->response->errorNotFound();

// 内部错误, 状态码为500
// 该方法可以传递一个参数，为该错误的自定义消息
return $this->response->errorInternal();

```

#### 添加其他响应数据

##### 添加 Meta 数据

```php
return $this->response->item($user)->addMeta('foo', 'bar');
```

或者直接设置 Meta 数据的数组

```php
return $this->response->item($user)->setMeta($meta);
```

##### 设置响应状态码

```php
return $this->response->item($user)->setCode(200);
```

##### 添加额外的头信息

```php
// 提供两种设置方式
return $this->response->item($user)->addHeader('X-Foo', 'Bar');
return $this->response->item($user)->addHeader(['X-Foo' => 'Bar']);
```

##### 设置 LastModified

```php
return $this->response->item($user)->setLastModified($time);
```

##### 设置 ETag

```php
return $this->response->item($user)->setETag($eTag);
```

##### 设置 Expires

```php
return $this->response->item($user)->setExpires($time);
```

##### 页面缓存控制

```php
return $this->response->item($user)->setCacheControl($cache);
```

### 响应数据格式

`think-api` 提供了两种返回格式, 可以通过修改配置文件`api.php` 中的`serializer`来设置

- `DataArray`
- `Array`

##### DataArray

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

##### Array

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

## 数据过滤

在`Zewail\Api\Api` trait的`$response`方法中，其中`item ` `collection`  `paginator`具有两个参数，第一个参数为模型数据，第二个参数为数据过滤列表

```php
// 如查询出来的$user具有id, name, age, mobile等属性
// 在设置了第二个参数为['id', 'name', 'age']后，将会过滤其他属性，只返回给接口列出的属性
return $this->response->item($user, ['id', 'name', 'age']);
return $this->response->collection($users, ['id', 'name', 'age']);
return $this->response->paginator($users, ['id', 'name', 'age']);
```

#### 集中管理

think-api`还提供了一个配置文件用于数据过滤或者说是数据资源的集中管理

使用该功能需要在thinkphp中新建一个配置文件`resources.php`

```php
return [
  // 用户相关接口
  // 例如设置一些用户的相关接口资源
  'user.age' => ['id', 'name', 'age'],
  'user.mobile' => ['id', 'name', 'mobile'],
];
```

然后在返回接口数据的时候在`item ` `collection`  `paginator`第二个参数传入该标识即可

```php
// 返回{'data': {'id':1, 'name': 'xiaoming', 'age': 20}}
return $this->response->item($user, 'user.age');
// 返回{'data': {'id':1, 'name': 'xiaoming', 'mobile': '13777777777'}}
return $this->response->item($user, 'user.mobile');
```

现在，哪些接口返回了哪些数据，在该配置文件中一目了然


### JWT

#### 签发Token

```php
// ...
// 验证账号密码

// 建议属性，非必须
$payload = [
  "iss" => "zewail.me", // Issuser: JWT签发者
  "sub" => "zewail.me", // Subject: JWT主体/所有人
  "aud" => "zewail", // Audience: JWT接收对象
  "exp" => "1431000000", // Expiration time: 时间戳, 代表过期时间
  "nbf" => "1421000000", // Not Before: 时间戳，代表开始生效的时间
  "iat" => "1421000000", // Issued at: 时间戳, 代表这个JWT的签发时间
  "jti" => "zewail.me", // JWT ID: JWT的唯一标识
];
// 签发token
$token = $this->jwt->encode($payload);
return $this->response->(['token' => $token]);
```

#### 验证Token

```php
// 从客户端传递过来的token
$token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJleGFtcGxlLm9yZyIsImF1ZCI6ImV4YW1wbGUuY29tIiwiaWF0IjoxMzU2OTk5NTI0LCJuYmYiOjEzNTcwMDAwMDB9.pv1CxzucYves3PMbyTflEi-zEqEHxkVvPl5DTf2bLC_jNibey3MlPrw6HqT3lZIc_BPuvLw3acLIDKJPV6UcFCCNBFwIoN0ewftIk4T0dTrOTpigSMzjvI-nXfzgoDL6xaqZ6Q4fmJm_oyHiJMfpNwuW06leq4CPBOb6fYoURXfOzQ8R9EOPCHtPAzoskKLllRtxda_LFH-3Yey9wATfWLfLMbCZkFwzlefJMDWU2D0DsW1upfYRaN7hI2-uLOQlAje21SIOYVDNWYgeaBsRRZ_Neg1yP2PBILyaHBM7tu_G3orDsUNjhTaVyDg3NZ01fwPibyecEnoLok9JQeq3qfA';
// 验证token, 失败会抛出异常, 也可以使用try-catch捕获异常
// $payload为一个对象
$payload = $this->jwt->decode($token);
// ...
// 后续操作
```

#### 加密算法

默认加密算法为`HMAC` 使用`SHA-256`算法加密，可在配置文件中修改加密算法

如果使用`openssl`加密，则需要配置私钥和公钥

目前支持:

- `HS256`: `HMAC 使用 SHA-256 算法加密`
- `HS512`: `HMAC 使用 SHA-512 算法加密`
- `RS256`: `RSASSA 使用 SHA-256 算法加密 `



### 授权协议

license: [MIT 授权协议](LICENSE)
author: [陈泽韦](http://www.zewail.me/)
email: [chanzewail@gmail.com](mailto:chanzewail@gmail.com)