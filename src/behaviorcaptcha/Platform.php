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

namespace think\behaviorcaptcha;

/**
 * 平台抽象类
 */
abstract class Platform implements PlatformInterface
{
	/**
     * 平台配置参数
     * @var array
     */
	protected $options = [];

    /**
     * 架构函数
     * @access public
     * @param array $options 平台配置参数
     */
    public function __construct(array $options = [])
    {
        // 合并配置参数
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }

        // 初始化
        $this->init();
    }

	/**
     * 动态设置平台配置参数
     * @access public
     * @param array $options 平台配置
     * @return $this
     */
    public function setConfig(array $options)
    {
        // 合并配置
        if (!empty($options)) {
            $this->options = array_merge($this->options, $options);
        }
        // 返回
        return $this->init();
    }

	/**
     * 初始化
     * @access protected
     * @return $this
     */
    protected function init()
    {
        // 返回
        return $this;
    }

	public function __call($method, $args)
    {
        return call_user_func_array([$this->handler, $method], $args);
    }
}