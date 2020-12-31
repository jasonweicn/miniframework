<?php
// +---------------------------------------------------------------------------
// | Mini Framework
// +---------------------------------------------------------------------------
// | Copyright (c) 2015-2021 http://www.sunbloger.com
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
     * MD5签名加盐(建议改掉默认值)
     *
     * @var string
     */
    public $salt = 'PRi6LN^p!#C7UI5&';

    /**
     * 签名过期时间
     *
     * @var int
     */
    private $expireTime = 300;

    /**
     * 设置签名过期时间
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
     * 校验签名
     *
     * @param string $type post|get
     * @return boolean
     */
    public function verifySign($type = 'post')
    {
        $sign = '';
        $signTime = 0;

        if ($type == 'get') {
            $data = $_GET;
        } elseif ($type == 'post') {
            $data = $_POST;
        }

        if (isset($data['sign'])) {
            $sign = $data['sign'];
            unset($data['sign']);
        } else {
            return false;
        }

        if (isset($data['signTime'])) {
            $signTime = $data['signTime'];
            if (! isTimestamp($signTime)) {
                return false;
            }

            if (($signTime + $this->expireTime) <= time()) {
                return false;
            }
        } else {
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
     * @param array $data
     * @return string
     */
    public function sign(array &$data)
    {
        // 1.对数组进行排序
        ksort($data);

        // 2.拼接成字符串
        $tmp = [];
        foreach ($data as $key => $val) {
            $tmp[] = $key . '=' . $val;
        }
        $dataStr = implode('&', $tmp);

        // 3.字符串加盐并通过MD5生成签名
        $sign = md5($dataStr . '|' . $this->salt);

        return $sign;
    }
}
