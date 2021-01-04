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

/**
 * 获取客户端IP地址
 *
 * @return NULL | string
 */
function getClientIp()
{
    $ip = null;

    if ($ip !== null) {
        return $ip;
    }
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
    $matches = [];
    preg_match("/[\d\.]{7,15}/", $ip, $matches);
    $ip = ! empty($matches[0]) ? $matches[0] : '0.0.0.0';

    return $ip;
}

/**
 * 改变数组KEY
 *
 * @param array $array
 * @param mixed $field
 * @return array
 */
function chgArrayKey($array, $field)
{
    $tmp = [];
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
 * @return string
 */
function getRandomString($len = 8)
{
    $str = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
    $strLen = strlen($str);
    $randomString = '';
    if (! is_int($len) || $len <= 0)
        $len = 8;
    for ($i = 0; $i < $len; $i ++) {
        $randomString .= substr($str, rand(0, $strLen - 1), 1);
    }

    return $randomString;
}

/**
 * 对图片进行base64编码转换
 *
 * @param string $image_file
 * @return string
 */
function base64EncodeImage($image_file)
{
    $base64_image = '';
    if (is_file($image_file)) {
        $image_info = getimagesize($image_file);
        $image_data = fread(fopen($image_file, 'r'), filesize($image_file));
        $base64_image = 'data:' . $image_info['mime'] . ';base64,';
        $base64_image .= chunk_split(base64_encode($image_data));
    } else {
        return false;
    }

    return $base64_image;
}

/**
 * 输出JSON
 *
 * @param mixed $data
 * @param boolean $push (true: echo & die | false: return)
 */
function pushJson($data, $push = true)
{
    if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
        $json = json_encode($data, JSON_UNESCAPED_UNICODE);
    } else {
        $json = json_encode($data);
    }

    if ($push === true) {
        header("Content-Type: application/json; charset=utf-8");
        echo $json;
        die();
    }

    return $json;
}

/**
 * 校验日期格式是否正确
 *
 * @param string $date 日期
 * @param string $formats 需要检验的格式数组
 * @return boolean
 */
function isDate($date, $formats = ['Y-m-d', 'Y/m/d'])
{
    $timestamp = strtotime($date);
    if (! $timestamp) {
        return false;
    }
    foreach ($formats as $format) {
        if (date($format, $timestamp) == $date) {
            return true;
        }
    }

    return false;
}

/**
 * 变量输出
 *
 * @param mixed $var
 * @param string $label
 * @param boolean $echo
 */
function dump($var, $label = null, $echo = true)
{
    ob_start();
    var_dump($var);
    $output = ob_get_clean();
    $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);

    $cli = preg_match("/cli/i", PHP_SAPI) ? true : false;

    if ($cli === true) {
        $output = PHP_EOL . $label . PHP_EOL . $output . PHP_EOL;
    } else {
        $output = '<pre>' . PHP_EOL . $label . PHP_EOL . $output . '</pre>' . PHP_EOL;
    }

    if ($echo) {
        echo $output;
    }

    return $output;
}

/**
 * 输出XML
 *
 * @param mixed $data 数据
 * @param boolean $push (true: echo & die | false: return) 是否立即显示并终止程序
 * @param boolean $indent 是否格式化缩进
 * @param string $root 根标签名称
 * @param array $attr 根标签属性数组
 * @param string $item 项目标签名称
 * @param string $id 当数据为索引数组时，项目标签属性的名称
 * @param string $encoding 编码
 * @return string
 */
function pushXml($data, $push = true, $indent = false, $root = 'data', $attr = [], $item = 'item', $id = 'id', $encoding = 'utf-8')
{
    $eol = ($indent === true) ? PHP_EOL : '';

    $_attr = '';
    foreach ($attr as $key => $val) {
        $_attr .= ' ' . $key . '="' . $val . '"';
    }

    $xml = '<?xml version="1.0" encoding="' . $encoding . '"?>' . $eol;
    $xml .= '<' . $root . $_attr . '>' . $eol;
    $xml .= parseDataToXml($data, $item, $id, $indent);
    $xml .= '</' . $root . '>';

    if ($push === true) {
        echo $xml;
        die();
    }

    return $xml;
}

/**
 * 数据转换XML
 *
 * @param mixed $data
 * @param string $item
 * @param string $id
 * @param string $indent
 * @param int $level
 * @return string
 */
function parseDataToXml($data, $item = 'item', $id = 'id', $indent = false, $level = 1)
{
    $eol = ($indent === true) ? PHP_EOL : '';
    $space = ($indent === true) ? str_repeat('  ', $level) : '';

    $xml = $attr = '';

    if (empty($data)) {
        return $xml;
    }

    foreach ($data as $key => $val) {
        if (is_int($key) && $key >= 0) {
            if (! empty($id)) {
                $attr = ' ' . $id . '="' . $key . '"';
            }

            $key = $item;
        }

        $xml .= $space . '<' . $key . $attr . '>';

        if (is_array($val) || is_object($val)) {
            $level ++;
            $xml .= $eol . parseDataToXml($val, $item, $id, $indent, $level);
            $level --;
            $xml .= $space . '</' . $key . '>' . $eol;
        } else {
            $xml .= $val;
            $xml .= '</' . $key . '>' . $eol;
        }
    }

    return $xml;
}

/**
 * 校验图片是否有效
 *
 * @param string $file
 * @return boolean
 */
function isImage($file)
{
    $tmp = getimagesize($file);
    switch ($tmp['mime']) {
        case 'image/jpeg':
            $img = imagecreatefromjpeg($file);
            break;
        case 'image/gif':
            $img = imagecreatefromgif($file);
            break;
        case 'image/png':
            $img = imagecreatefrompng($file);
            break;
        default:
            return false;
            break;
    }
    if ($img == false) {
        return false;
    } else {
        return true;
    }
}

/**
 * 获取字符串长度
 *
 * @param string $string
 * @return int
 */
function getStringLen($string)
{
    return (strlen($string) + mb_strlen($string, 'UTF8')) / 2;
}

/**
 * 让浏览器下载文件
 *
 * @param string $file 文件路径
 * @param string $customName 自定义文件名
 * @return string | boolean
 */
function browserDownload($file, $customName = null)
{
    if (file_exists($file)) {
        $filename = empty($customName) ? basename($file) : $customName;
        header('Content-length: ' . filesize($file));
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        readfile($file);
        die();
    } else {
        return false;
    }
}

/**
 * 获取文件扩展名
 *
 * @param string $filename
 * @return string
 */
function getFileExtName($filename)
{
    $tmp = explode('.', $filename);

    return $tmp[count($tmp) - 1];
}

/**
 * 获取一个指定长度INT类型HASH值
 *
 * @param string $s
 * @param number $len
 * @return number
 */
function getHash($s, $len = 4)
{
    $h = sprintf('%u', crc32($s));

    return intval(fmod($h, $len));
}

/**
 * 转换特殊字符为HTML实体字符
 *
 * @param string $string
 * @param boolean $doubleEncode
 * @return string
 */
function htmlEncode($string, $doubleEncode = true)
{
    if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
        $reVal = htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', $doubleEncode);
    } else {
        $reVal = htmlspecialchars($string, ENT_QUOTES, 'UTF-8', $doubleEncode);
    }

    return $reVal;
}

/**
 * 判断一个数组是否为索引数组
 *
 * @param array $array
 * @return boolean
 */
function isIndexArray($array)
{
    $i = 0;
    $keys = array_keys($array);
    foreach ($keys as $key) {
        if (! is_int($key) || $i != $key) {
            return false;
        }
        $i ++;
    }

    return true;
}

/**
 * 判断一个字符串是否为UNIX时间戳格式
 * 
 * @param string $timestamp
 * @return boolean
 */
function isTimestamp($timestamp)
{
    if (strtotime(date('Y-m-d H:i:s', $timestamp)) === (int)$timestamp) {
        return true;
    }
    
    return false;
}

/**
 * 将数组转换为URL GET请求参数(例如：a=1&b=2&c=3)
 * 
 * @param array $array
 * @param int $type 拼接方式，默认1为常规，2、3和4为伪静态
 * @return string
 */
function arrayToUrlParams(array $array, $type = 1)
{
    switch ($type) {
        case 1:
            $joinSymbol = '=';
            $splitSymbol = '&';
            break;
        case 2:
            $joinSymbol = $splitSymbol = '/';
            break;
        case 3:
            $joinSymbol = $splitSymbol = '_';
            break;
        case 4:
            $joinSymbol = $splitSymbol = '-';
            break;
    }
    $tmp = [];
    foreach ($array as $key => $val) {
        if (is_array($val)) {
            continue;
        }
        $tmp[] = $key . $joinSymbol . $val;
    }
    return implode($splitSymbol, $tmp);
}
