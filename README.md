# ThinkPHP 行为验证码

一个简单的ThinkPHP 行为验证码扩展

主要功能：

支持多平台配置：目前支持极验(Geetest)、Vaptcha平台；

可扩展自定义平台驱动；

支持facade门面方式调用；

支持动态切换平台；

## 安装

~~~
composer require axguowen/think-behaviorcaptcha
~~~

## 用法示例

本扩展不能单独使用，依赖ThinkPHP6.0+

首先配置config目录下的behaviorcaptcha.php配置文件，然后可以按照下面的用法使用。

### 生成验证码数据

~~~php

use think\facade\BehaviorCaptcha;

// 生成极验验证码数据
$createResult = BehaviorCaptcha::create();
// 成功
if(!is_null($createResult[0])){
    print_r($createResult[0]);
}
else{
    echo $createResult[1]->getMessage();
}

~~~

### 验证码校验

~~~php

$verifyResult = BehaviorCaptcha::verify([
    'geetest_challenge' => 'xxxx',
    'geetest_validate' => 'xxxx',
    'geetest_seccode' => 'xxxx',
]);

// 成功
if(!is_null($verifyResult[0])){
    print_r($verifyResult[0]);
}
else{
    echo $verifyResult[1]->getMessage();
}
~~~

### 动态切换平台

~~~php

use think\facade\BehaviorCaptcha;

// 使用Vaptcha平台
$verifyResult = BehaviorCaptcha::platform('vaptcha')->verify([
    'vaptcha_server' => 'xxxx',
    'vaptcha_token' => 'xxxx',
]);

// 成功
if(!is_null($verifyResult[0])){
    print_r($verifyResult[0]);
}
else{
    echo $verifyResult[1]->getMessage();
}

~~~

## 配置说明

~~~php

// 验证码配置
return [
    // 默认验证平台
    'default' => 'geetest',
    // 验证平台配置
    'platforms' => [
        // 极验平台
        'geetest' => [
            // 驱动类型
            'type' => 'Geetest',
            // 极验验证ID
            'captcha_id' => '',
            // 极验私钥
            'private_key' => '',
            // 加密模式, 支持md5/sha256/hmac-sha256, 默认为md5
            'digestmod' => '',
            // 二次验证challenge字段名, 默认geetest_challenge
            'challenge_field' => '',
            // 二次验证validate字段名, 默认geetest_validate
            'validate_field' => '',
            // 二次验证seccode字段名, 默认geetest_seccode
            'seccode_field' => '',
        ],
        // vaptcha平台
        'vaptcha' => [
            // 驱动类型
            'type' => 'Vaptcha',
            // 验证单元的VID
            'vid' => '',
            // 验证单元的KEY
            'key' => '',
            // 验证场景
            'scene' => 0,
            // 二次验证server字段名 默认server
            'server_field' => '',
            // 二次验证token字段名 默认token
            'token_field' => '',
        ],
    ],
];

~~~

## 自定义平台驱动

如果需要扩展自定义验证平台驱动，需要继承\think\behaviorcaptcha\PlatformInterface类, 并实现create方法和verify方法。

具体代码可以参考现有的平台驱动

扩展自定义驱动后，只需要在配置文件behaviorcaptcha.php中设置default的值为该驱动类名（包含命名空间）即可。