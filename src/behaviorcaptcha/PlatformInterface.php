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
 * Platform interface
 */
interface PlatformInterface
{
    /**
     * 创建验证码
     * @access public
     * @param array $options
     * @return array
     */
    public function create(array $options = []);

    /**
     * 验证验证码
     * @access public
     * @param array $options
     * @return array
     */
    public function verify(array $options = []);
}
