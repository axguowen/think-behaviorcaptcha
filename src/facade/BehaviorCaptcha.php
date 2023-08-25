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

namespace think\facade;

use think\Facade;

/**
 * @see \think\BehaviorCaptcha
 * @mixin \think\BehaviorCaptcha
 */
class BehaviorCaptcha extends Facade
{
    /**
     * 获取当前Facade对应类名
     * @access protected
     * @return string
     */
    protected static function getFacadeClass()
    {
        return \think\BehaviorCaptcha::class;
    }
}