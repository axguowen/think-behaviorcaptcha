<?php
// +----------------------------------------------------------------------
// | ThinkPHP BehaviorCaptcha [Simple Behavior Captcha for ThinkPHP]
// +----------------------------------------------------------------------
// | ThinkPHP 行为验证码扩展
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: axguowen <axguowen@qq.com>
// +----------------------------------------------------------------------

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
            'digestmod' => 'md5',
            // 二次验证challenge字段名, 默认geetest_challenge
            'challenge_field' => 'geetest_challenge',
            // 二次验证validate字段名, 默认geetest_validate
            'validate_field' => 'geetest_validate',
            // 二次验证seccode字段名, 默认geetest_seccode
            'seccode_field' => 'geetest_seccode',
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
            // 二次验证server字段名 默认vaptcha_server
            'server_field' => 'vaptcha_server',
            // 二次验证token字段名 默认vaptcha_token
            'token_field' => 'vaptcha_token',
        ],
        // ajcaptcha平台
        'ajcaptcha' => [
            // 驱动类型
            'type' => 'Ajcaptcha',
            // 验证码类型, slide滑动验证, click点选验证
            'verify_type' => 'click',
            // 自定义字体包路径, 不填使用默认值
            'font_file' => '',
            // 背景图片路径, 不填使用默认值, 支持string与array两种数据结构, string为默认图片的目录, array索引数组则为具体图片的地址
            'backgrounds' => [],
            // 点击验证码文字数量
            'click_word_length' => 5,
            // 点击验证码验证文字数量
            'click_check_length' => 4,
            // 滑动验证码模板图, 格式同上支持string与array
            'slide_templates' => [],
            // 滑动验证码容错偏移量
            'slide_offset' => 10,
            // 是否开启缓存图片像素值，开启后能提升服务端响应性能（但要注意更换图片时，需要清除缓存）
            'slide_cache_pixel' => true, 
            // 水印文字内容
            'watermark_text' => '',
            // 水印文字大小
            'watermark_fontsize' => 12,
            // 水印文字颜色
            'watermark_color' => '#ffffff',
            // 缓存驱动类
            'cache_handler' => '',
            // 缓存方法映射
            'cache_method_map' => [],
            // 缓存驱动配置参数
            'cache_options' => [],
        ],
    ],
];
