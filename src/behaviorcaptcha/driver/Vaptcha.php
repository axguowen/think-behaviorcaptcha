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

use think\facade\Request;
use think\behaviorcaptcha\Platform;
use axguowen\HttpClient;

class Vaptcha extends Platform
{
    /**
     * 平台配置参数
     * @var array
     */
    protected $options = [
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
    ];

    /**
     * 初始化
     * @access protected
     * @return $this
     */
    protected function init()
    {
        if (empty($this->options['server_field'])){
            $this->options['server_field'] = 'vaptcha_server';
        }
        if (empty($this->options['token_field'])){
            $this->options['token_field'] = 'vaptcha_token';
        }
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
        // 返回配置
        return [[
            'vid' => $this->options['vid'],
            'scene' => $this->options['scene'],
        ], null];
    }

    /**
     * 验证验证码
     * @access public
     * @param array $options
     * @return array
     */
    public function verify(array $options = [])
    {
        // 如果参数中没有server参数
        if(!isset($options[$this->options['server_field']])){
            return [null, new \Exception('[' . $this->options['server_field'] . '] 参数不存在')];
        }
        // 如果参数中没有token参数
        if(!isset($options[$this->options['token_field']])){
            return [null, new \Exception('[' . $this->options['token_field'] . '] 参数不存在')];
        }

        // 请求参数
        $data = json_encode([
            'id' => $this->options['vid'],
            'secretkey' => $this->options['key'],
            'scene' => $this->options['scene'],
            'token' => $options[$this->options['token_field']],
            'ip' => Request::ip(),
        ]);

        // 获取响应
        $response = HttpClient::post($options[$this->options['server_field']], $data, [
            'Content-Type' => 'application/json; charset=utf-8',
            'Content-Length' => strlen($data)
        ]);
        // 响应错误
        if (!$response->ok()) {
            // 返回异常
            return [null, new \Exception($response->error, $response->statusCode)];
        }
        // 获取响应体
        $responseBody = !is_null($response->body) ? $response->json() : [];

        // 验证通过
        if(isset($responseBody['success']) && $responseBody['success'] == 1){
            // 返回成功
            return ['验证通过', null];
        }
        // 返回失败
        return [null, new \Exception('验证不通过')];
    }
}
