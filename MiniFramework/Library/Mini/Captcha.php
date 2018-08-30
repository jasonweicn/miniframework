<?php
// +---------------------------------------------------------------------------
// | Mini Framework
// +---------------------------------------------------------------------------
// | Copyright (c) 2015-2018 http://www.sunbloger.com
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
// | Source: https://github.com/jasonweicn/MiniFramework
// +---------------------------------------------------------------------------
// | Author: Jason Wei <jasonwei06@hotmail.com>
// +---------------------------------------------------------------------------
// | Website: http://www.sunbloger.com/miniframework
// +---------------------------------------------------------------------------
namespace Mini;

class Captcha
{

    /**
     * 验证码图片宽度
     *
     * @var int
     */
    private $_imX = 180;

    /**
     * 验证码图片高度
     *
     * @var int
     */
    private $_imY = 60;
    
    /**
     * 字体
     * 
     * @var string
     */
    private $_font;
    
    /**
     * 存储验证码的会话名称
     * 
     * @var string
     */
    private $_sessionKey;
    
    public function __construct()
    {
        $this->_font = APP_PATH . '/Public/font/aleo-bold-webfont.ttf';
        
        $this->_sessionKey = 'Mini_' . APP_NAMESPACE . '_Captcha_Code';
    }
    
    /**
     * 设置字体
     * 
     * @param string $font
     * @return bool
     */
    public function setFont($font)
    {
        if (empty($font)) {
            return false;
        }
        
        $this->_font = $font;
        
        return true;
    }
    
    /**
     * 设置验证码图片尺寸
     * 
     * @param int $x
     * @param int $y
     * @return bool
     */
    public function setImgSize($x, $y)
    {
        if (! is_int($x) || ! is_int($y)) {
            return false;
        }
        
        $this->_imX = $x;
        $this->_imY = $y;
        
        return true;
    }
    
    /**
     * 创建验证码图片
     * 
     * @param int $len
     * @param bool $push
     * @return resource
     */
    public function create($len = 4, $push = true)
    {
        $str = '3456789ABCDEFGHJKMNPRSTWXYabcdeghkmnpqwxy';
        $strLen = strlen($str);
        $text = '';
        for ($i = 0; $i < $len; $i ++) {
            $num = mt_rand(0, $strLen - 1);
            $text .= substr($str, $num, 1);
        }
        
        Session::start();
        Session::set($this->_sessionKey, strtolower($text));
        
        // 画布
        $im = imagecreatetruecolor($this->_imX, $this->_imY);
        $bgColor = imagecolorallocate($im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255));
        imagefill($im, 0, 0, $bgColor);
        
        // 计算字号
        $fontBox = floor($this->_imX / $len);
        if ($fontBox >= $this->_imY - floor($this->_imY / 3)) {
            $fontMax = $this->_imY - floor($this->_imY / 3);
        } else {
            $fontMax = $fontBox;
        }
        
        for ($i = 0; $i < strlen($text); $i ++) {
            $tmp = substr($text, $i, 1); // 字符
            $array = array(-1, 1);
            $p = array_rand($array);
            $an = $array[$p] * mt_rand(1, 20); // 角度
            $size = mt_rand(ceil($fontMax / 2), $fontMax); // 字号
            if ($i == 0) {
                $posX = floor($fontMax / 4); // X方向位置
            } else {
                $posX = mt_rand($i * $fontBox - ceil($fontBox / 2), $i * $fontBox);
            }
            $baseY = ceil($this->_imY / 2) + ($size / 2);
            $posLimit = floor(($this->_imY - $size) / 2);
            $posY = mt_rand($baseY - $posLimit, $baseY + $posLimit); // Y方向位置
                                     
            // 字符颜色和透明度
            $col = imagecolorallocatealpha($im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), mt_rand(10, 50));
            
            imagettftext($im, $size, $an, $posX, $posY, $col, $this->_font, $tmp);
        }
        
        // 干扰线
        $lineNum = mt_rand(2, 6);
        for ($i = 1; $i <= $lineNum; $i ++) {
            imagesetthickness($im, mt_rand(1, 3));
            $col = imagecolorallocatealpha($im, mt_rand(0, 255), mt_rand(0, 255), mt_rand(0, 255), mt_rand(40, 50));
            imageline($im, mt_rand(1, $this->_imX), mt_rand(1, $this->_imY), mt_rand(1, $this->_imX), mt_rand(1, $this->_imY), $col);
        }
        
        if ($push === true) {
            $this->push($im);
        } else {
            return $im;
        }
    }
    
    /**
     * 输出
     * 
     * @param resource $im
     */
    public function push($im)
    {
        // 设置文件头;
        header('Content-type: image/png');
        
        // 以PNG格式将图像输出到浏览器或文件;
        imagepng($im);
        
        // 销毁一图像,释放与image关联的内存;
        imagedestroy($im);
        
        die();
    }
    
    /**
     * 校验
     * 
     * @param string $code
     * @return bool
     */
    public function check($code)
    {
        Session::start();
        $sessCode = Session::get($this->_sessionKey);
        
        if ($sessCode != null) {
            Session::set($this->_sessionKey, null);
            if ($sessCode == strtolower($code)) {
                return true;
            }
        }
        
        return false;
    }
}
