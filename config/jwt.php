<?php 
return [
    // 加密算法
    'algorithm' => 'HS256',
    // HMAC算法使用的加密字符串
    'key' => 'ex-key',
    // RSA算法使用的私钥文件路径
    'privateKeyPath' => '/home/rsa_private_key.pem',
    // RSA算法使用的公钥文件路径
    'publicKeyPath' => '/home/rsa_public_key.pem',
    // 误差时间，单位秒
    'deviation' => 60,
    // 过期时间, 单位分钟
    'ttl' => 120,
    // 用户模型路径
    'user' => app\index\model\User::class,
];