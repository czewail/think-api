<?php 
return [
	// api 默认版本号
	'api_version' => 'v1',

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

	// 加密密钥
	'jwt_key' => 'ex-key',

	// openssl 私钥路径
	// 必须绝对路径
	'jwt_privateKeyPath' => '/Users/czw/Developer/Composer/tp5/application/extra/rsa_private_key.pem',

	// openssl 公钥路径
	// 必须绝对路径
	'jwt_publicKeyPath' => '/Users/czw/Developer/Composer/tp5/application/extra/rsa_public_key.pem',

	// jwt 加密算法
	// 可选 HS256 HS512 RS256
	'jwt_algorithm' => 'RS256',

	// 允许误差时间（单位秒）
	'jwt_deviation' => 0,
];