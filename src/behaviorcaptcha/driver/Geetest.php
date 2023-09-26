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

class Geetest extends Platform
{
    // 基础URL
    const BASE_URL = 'http://api.geetest.com';
    // SDK版本
    const SDK_VERSION = 'php-laravel:3.1.0';

	/**
     * 平台配置参数
     * @var array
     */
    protected $options = [
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
    ];

	/**
     * 初始化
     * @access protected
     * @return $this
     */
    protected function init()
    {
        if (empty($this->options['challenge_field'])){
            $this->options['challenge_field'] = 'geetest_challenge';
        }
        if (empty($this->options['validate_field'])){
            $this->options['validate_field'] = 'geetest_validate';
        }
        if (empty($this->options['seccode_field'])){
            $this->options['seccode_field'] = 'geetest_seccode';
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
        // 从服务器获取流水号数据源
        $registerResult = $this->register();
        // 获取失败
        if(is_null($registerResult[0])){
            return $registerResult;
        }
        // 获取数据
        $originChallenge = $registerResult[0];
        // 流水号数据源为空或者值为0代表失败
        if (empty($originChallenge)) {
            // 本地随机生成32位字符串
            $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
            // 验证码流水号
            $challenge = '';
            for ($i = 0; $i < 32; $i++) {
                $challenge .= $characters[rand(0, strlen($characters) - 1)];
            }
            // 返回
            return [[
                'success' => 0,
                'gt' => $this->options['captcha_id'],
                'challenge' => $challenge,
                'new_captcha' => true
            ], null];
        }
        
        // 验证码流水号
        $challenge = null;
        switch($this->options['digestmod']){
            case 'md5':
                $challenge = $this->md5Encode($originChallenge . $this->options['private_key']);
                break;
            case 'sha256':
                $challenge = $this->sha256Encode($originChallenge . $this->options['private_key']);
                break;
            case 'hmac-sha256':
                $challenge = $this->hmacSha256Encode($originChallenge, $this->options['private_key']);
                break;
            default:
                $challenge = $this->md5Encode($originChallenge . $this->options['private_key']);
        }
        // 返回
        return [[
            'success' => 1,
            'gt' => $this->options['captcha_id'],
            'challenge' => $challenge,
            'new_captcha' => true
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
        // 如果参数中没有流水号
        if(!isset($options[$this->options['challenge_field']])){
            return [null, new \Exception('[' . $this->options['challenge_field'] . '] 参数不存在')];
        }
        // 如果参数中没有验证数据
        if(!isset($options[$this->options['validate_field']])){
            return [null, new \Exception('[' . $this->options['validate_field'] . '] 参数不存在')];
        }
        // 如果参数中没有加密代码
        if(!isset($options[$this->options['seccode_field']])){
            return [null, new \Exception('[' . $this->options['seccode_field'] . '] 参数不存在')];
        }
        
        // 获取参数中的流水号
        $challenge = $options[$this->options['challenge_field']];
        // 获取参数中的验证数据
        $validate = $options[$this->options['validate_field']];
        // 获取参数中的加密代码
        $seccode = $options[$this->options['seccode_field']];
        
        // 如果服务可用
        if(true === $this->serverCheck()){
            // 在线校验
            return $this->verifyOnline($challenge, $validate, $seccode);
        }
        // 离线校验
        return $this->verifyOffline($challenge, $validate, $seccode);
    }

    /**
     * 从极验服务器获取验证码流水号数据源
     * @access protected
     * @return mixed
     */
    protected function register()
    {
        // 初始化流水号
        $originChallenge = 0;
        // 如果服务不可用
        if(true === $this->serverCheck()){
            // 请求参数
            $data = [
                'user_id' => '',
                'client_type' => Request::isMobile() ? 'h5' : 'web',
                'ip_address' => Request::ip(),
                'gt' => $this->options['captcha_id'],
                'sdk' => self::SDK_VERSION,
                'json_format' => 1,
                'digestmod' => $this->options['digestmod'],
            ];

            // 获取响应
            $response = HttpClient::get(self::BASE_URL . '/register.php?' . http_build_query($data));
            // 错误
            if (!$response->ok()) {
                // 返回异常
                return [null, new \Exception($response->error, $response->statusCode)];
            }
            // 获取响应体
            $responseBody = !is_null($response->body) ? $response->json() : [];
            // 存在流水号
            if(isset($responseBody['challenge'])){
                $originChallenge = $responseBody['challenge'];
            }
        }
        // 返回
        return [$originChallenge, null];
    }

    /**
     * 正常流程下（即验证初始化成功），二次验证
     * @access protected
     * @param string $challenge 流水号
     * @param string $validate 验证数据
     * @param string $seccode 加密代码
     * @return bool
     */
    protected function verifyOnline($challenge, $validate, $seccode)
    {
        // 构造验证码流水号加密串
        $secChallenge = null;
        $secSeccode = null;
        // 构造加密代码的加密串
        switch($this->options['digestmod']){
            case 'md5':
                $secChallenge = $this->md5Encode($this->options['private_key'] . 'geetest' . $challenge);
                $secSeccode = $this->md5Encode($seccode);
                break;
            case 'sha256':
                $secChallenge = $this->sha256Encode($this->options['private_key'] . 'geetest' . $challenge);
                $secSeccode = $this->sha256Encode($seccode);
                break;
            case 'hmac-sha256':
                $secChallenge = $this->hmacSha256Encode('geetest' . $challenge, $this->options['private_key']);
                $secSeccode = $this->hmacSha256Encode($seccode, $this->options['private_key']);
                break;
            default:
                $secChallenge = $this->md5Encode($this->options['private_key'] . 'geetest' . $challenge);
                $secSeccode = $this->md5Encode($seccode);
        }
        // 本地校验验证串是否正确
        if($secChallenge != $validate){
            // 返回失败
            return [null, new \Exception('验证不通过')];
        }

        // 请求参数
        $data = [
            'user_id' => '',
            'client_type' => Request::isMobile() ? 'h5' : 'web',
            'ip_address' => Request::ip(),
            'seccode' => $seccode,
            'json_format' => 1,
            'challenge' => $challenge,
            'sdk' => self::SDK_VERSION,
            'captchaid' => $this->options['captcha_id']
        ];

        // 获取响应
        $response = HttpClient::post(self::BASE_URL . '/validate.php', http_build_query($data), ['Content-Type' => 'application/x-www-form-urlencoded']);
        // 响应错误
        if (!$response->ok()) {
            // 返回异常
            return [null, new \Exception($response->error, $response->statusCode)];
        }
        // 获取响应体
        $responseBody = !is_null($response->body) ? $response->json() : [];
        // 响应体不存在加密代码
        if(isset($responseBody['seccode']) && $responseBody['seccode'] == $secSeccode){
            // 返回成功
            return ['验证通过', null];
        }
        // 返回失败
        return [null, new \Exception('验证不通过')];
    }

    /**
     * 异常流程下（即验证初始化失败，宕机模式），二次验证
     * 注意：由于是宕机模式，初衷是保证验证业务不会中断正常业务，所以此处只作简单的参数校验，可自行设计逻辑。
     * @access protected
     * @param string $challenge 流水号
     * @param string $validate 验证数据
     * @param string $seccode 加密代码
     * @return bool
     */
    protected function verifyOffline($challenge, $validate, $seccode)
    {
        // 本地验证串校验通过
        if($this->md5Encode($challenge) == $validate){
            // 返回成功
            return ['验证通过', null];
        }
        // 返回失败
        return [null, new \Exception('验证不通过')];
    }

    /**
     * 检测极验服务器是否宕机
     * @access protected
     * @return bool
     */
    protected function serverCheck()
    {
        // 获取响应
        $response = HttpClient::get('https://bypass.geetest.com/v1/bypass_status.php?gt=' . $this->options['captcha_id']);
        // 错误
        if (!$response->ok()) {
            // 返回异常
            return false;
        }
        // 获取响应体
        $responseBody = !is_null($response->body) ? $response->json() : [];
        // 状态为成功
        if($responseBody['status'] == 'success'){
            return true;
        }
        // 返回失败
        return false;
    }

    /**
     * md5 加密
     * @access protected
     * @param string $value 要加密的内容
     * @return string
     */
    protected function md5Encode($value)
    {
        return hash('md5', $value);
    }

    /**
     * sha256加密
     * @access protected
     * @param string $value 要加密的内容
     * @return string
     */
    protected function sha256Encode($value)
    {
        return hash('sha256', $value);
    }

    /**
     * hmac-sha256 加密
     * @access protected
     * @param string $value 要加密的内容
     * @return string
     */
    protected function hmacSha256Encode($value, $key)
    {
        return hash_hmac('sha256', $value, $key);
    }
}