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

namespace think\behaviorcaptcha\driver;

use think\behaviorcaptcha\Platform;
use axguowen\Ajcaptcha as Captcha;

class Ajcaptcha extends Platform
{
	/**
     * 平台配置参数
     * @var array
     */
    protected $options = [
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
        // 二次验证token字段名, 默认ajcaptcha_token
        'token_field' => 'ajcaptcha_token',
        // 二次验证point字段名, 默认ajcaptcha_point
        'point_field' => 'ajcaptcha_point',
        // 二次验证encrypt字段名, 默认ajcaptcha_encrypt
        'encrypt_field' => 'ajcaptcha_encrypt',
    ];

	/**
     * 初始化
     * @access protected
     * @return $this
     */
    protected function init()
    {
        if (empty($this->options['token_field'])){
            $this->options['token_field'] = 'ajcaptcha_token';
        }
        if (empty($this->options['point_field'])){
            $this->options['point_field'] = 'ajcaptcha_point';
        }
        if (empty($this->options['encrypt_field'])){
            $this->options['encrypt_field'] = 'ajcaptcha_encrypt';
        }

        // 实例化验证码
        $this->handler = new Captcha($this->options);
        // 返回
        return $this;
    }

    /**
     * 创建验证码
     * @access public
     * @param array $options
     * @return array
     */
    public function create(array $options = [])
    {
        // 生成验证码
        $captchaData = $this->handler->get();
        // 返回
        return [$captchaData, null];
    }

    /**
     * 一次验证
     * @access public
     * @param array $options
     * @return array
     */
    public function check(array $options = [])
    {
        // 如果参数中没有token
        if(!isset($options[$this->options['token_field']])){
            return [null, new \Exception('[' . $this->options['token_field'] . '] 参数不存在')];
        }
        // 如果参数中没有point
        if(!isset($options[$this->options['point_field']])){
            return [null, new \Exception('[' . $this->options['point_field'] . '] 参数不存在')];
        }

        // 获取参数中的token
        $token = $options[$this->options['token_field']];
        // 获取参数中的point
        $point = $options[$this->options['point_field']];

        // 返回
        return $this->handler->check($token, $point);
    }

    /**
     * 二次验证
     * @access public
     * @param array $options
     * @return array
     */
    public function verify(array $options = [])
    {
        // 如果参数中没有token
        if(!isset($options[$this->options['token_field']])){
            return [null, new \Exception('[' . $this->options['token_field'] . '] 参数不存在')];
        }
        // 如果参数中没有point
        if(!isset($options[$this->options['point_field']])){
            return [null, new \Exception('[' . $this->options['point_field'] . '] 参数不存在')];
        }
        // 如果参数中没有encrypt
        if(!isset($options[$this->options['encrypt_field']])){
            return [null, new \Exception('[' . $this->options['encrypt_field'] . '] 参数不存在')];
        }

        // 获取参数中的token
        $token = $options[$this->options['token_field']];
        // 获取参数中的point
        $point = $options[$this->options['point_field']];
        // 获取参数中的encrypt
        $encrypt = $options[$this->options['encrypt_field']];

        // 返回
        return $this->handler->validate($encrypt, $token, $point);
    }
}