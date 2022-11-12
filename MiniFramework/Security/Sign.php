<?php
// +---------------------------------------------------------------------------
// | Mini Framework
// +---------------------------------------------------------------------------
// | Copyright (c) 2015-2022 http://www.sunbloger.com
// +---------------------------------------------------------------------------
// | Licensed under the Apache License, Version 2.0 (the "License");
// | you may not use this file except in compliance with the License.
// | You may obtain a copy of the License at
// |
// | http://www.apache.org/licenses/LICENSE-2.0
// |
// | Unless required by applicable law or agreed to in writing, software
// | distributed under the License is distributed on an "AS IS" BASIS,
// | WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
// | See the License for the specific language governing permissions and
// | limitations under the License.
// +---------------------------------------------------------------------------
// | Source: https://github.com/jasonweicn/miniframework
// +---------------------------------------------------------------------------
// | Author: Jason Wei <jasonwei06@hotmail.com>
// +---------------------------------------------------------------------------
// | Website: http://www.sunbloger.com/miniframework
// +---------------------------------------------------------------------------
namespace Mini\Security;

class Sign
{

    /**
     * 签名加盐
     *
     * @var string
     */
    public $salt;

    /**
     * 签名过期时间（秒）
     *
     * @var int
     */
    private $expireTime = 10;
    
    /**
     * 加密方式
     * 
     * @var string
     */
    private $encryptType = 'md5';

    /**
     * 设置签名过期时间（单位：秒）
     *
     * @param int $expireTime
     * @return boolean
     */
    public function setExpireTime($expireTime)
    {
        if (preg_match('/^\d+$/', $expireTime)) {
            $this->expireTime = $expireTime;
            return true;
        }

        return false;
    }
    
    /**
     * 设置一个自定义的 Salt 字符串
     * 
     * @param string $salt
     * @return boolean
     */
    public function setSalt(string $salt)
    {
        $this->salt = $salt;
        
        return true;
    }
    
    /**
     * 设置加密方式
     * 
     * @param string $type md5|sha1
     * @return \Mini\Security\Sign
     */
    public function setEncryptType($type)
    {
        if ($type == 'md5' || $type == 'sha1') {
            $this->encryptType = $type;
        }
        
        return $this;
    }

    /**
     * 校验签名
     *
     * @param string $type post|get|stream
     * @return boolean
     */
    public function verifySign($type = 'post')
    {
        $sign = '';
        $signTime = 0;
        
        if ($type == 'post' || $type == 'get') {
            $data = $type == 'post' ? $_POST : $_GET;
            if (isset($data['sign']) && isset($data['signTime'])) {
                $sign = $data['sign'];
                $signTime = $data['signTime'];
                unset($data['sign']);
            } else {
                return false;
            }
        } elseif ($type == 'stream') {
            $header = \Mini\Base\Request::getInstance()->getHeader();
            if ($header->has('X-Sign') && $header->has('X-Signtime')) {
                $sign = $header->get('X-Sign');
                $signTime = $header->get('X-Signtime');
            } else {
                return false;
            }
            $data = file_get_contents('php://input') . $signTime;
        }
        if (! isTimestamp($signTime)) {
            return false;
        }
        if (($signTime + $this->expireTime) <= time()) {
            return false;
        }
        if ($sign === $this->sign($data)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 生成签名
     *
     * @param mixed $data
     * @return string
     */
    public function sign($data)
    {
        $dataStr = '';
        if (is_array($data)) {
            ksort($data);
            $tmp = [];
            foreach ($data as $key => $val) {
                $tmp[] = $key . '=' . $val;
            }
            $dataStr = implode('&', $tmp);
        } else {
            $dataStr = $data;
        }
        $salt = isset($this->salt) ? $this->salt : APP_PATH;
        $sign = '';
        if ($this->encryptType == 'md5') {
            $sign = md5($dataStr . '|' . $salt);
        } else if ($this->encryptType == 'sha1') {
            $sign = sha1($dataStr . '|' . $salt);
        }

        return $sign;
    }
}
