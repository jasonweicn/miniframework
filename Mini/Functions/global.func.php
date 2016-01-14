<?php
// +------------------------------------------------------------
// | Mini Framework
// +------------------------------------------------------------
// | Source: https://github.com/jasonweicn/MiniFramework
// +------------------------------------------------------------
// | Author: Jason.wei <jasonwei06@hotmail.com>
// +------------------------------------------------------------

/**
 * 获取客户端IP地址
 * @return NULL|string|unknown
 */
function getClientIp ()
{
    $ip = null;

    if ($ip !== null) return $ip;
    if (isset($_SERVER)) {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            foreach ($arr as $ip) {
                $ip = trim($ip);
                if ($ip != 'unknown') {
                    $ip = $ip;
                    break;
                }
            }
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['HTTP_CDN_SRC_IP'])) {
            $ip = $_SERVER['HTTP_CDN_SRC_IP'];
        } else {
            if (isset($_SERVER['REMOTE_ADDR'])) {
                $ip = $_SERVER['REMOTE_ADDR'];
            } else {
                $ip = '0.0.0.0';
            }
        }
    } else {
        if (getenv('HTTP_X_FORWARDED_FOR')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('HTTP_CLIENT_IP')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_CDN_SRC_IP')) {
            $ip = getenv('HTTP_CDN_SRC_IP');
        } else {
            $ip = getenv('REMOTE_ADDR');
        }
    }
    preg_match("/[\d\.]{7,15}/", $ip, $matches);
    $ip = !empty($matches[0]) ? $matches[0] : '0.0.0.0';
    
    return $ip;
}

/**
 * 改变数组KEY
 * 
 * @param array $array
 * @param mixed $field
 */
function chgArrayKey ($array, $field)
{
    $tmp = array();
    if (is_array($array)) {
        foreach ($array as $value) {
            $tmp[$value[$field]] = $value;
        }
    } else {
        return false;
    }
    
    return $tmp;
}

/**
 * 获取一个指定长度的随机字符串
 * 
 * @param int $len
 */
function getRandomString ($len = 8)
{
    $str = '0123456789';
    $str.= 'abcdefghijklmnopqrstuvwxyz';
    $str.= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $strLen = strlen($str);
    $randomString = '';
    if (!is_int($len) || $len <= 0) $len = 8;
    for ($i=0; $i<$len; $i++) {
        $randomString .= $str[rand(0, $strLen-1)];
    }
    
    return $randomString;
}
?>