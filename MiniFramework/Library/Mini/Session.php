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

class Session
{

    /**
     * 开启会话
     *
     * @param array $params
     */
    public static function start($params = array())
    {
        $flag = false;
        if (version_compare(PHP_VERSION, '5.4.0', '>=')) {
            if (session_status() != 2) {
                $flag = true;
            }
        } else {
            if (! isset($_SESSION)) {
                $flag = true;
            }
        }
        
        if ($flag === true) {
            if (! is_array($params)) {
                throw new Exceptions('The session params must be an array.');
            }
            foreach ($params as $paramName => $paramValue) {
                ini_set('session.' . $paramName, $paramValue);
            }
            
            // 开启会话
            session_start();
        }
        
        return true;
    }

    /**
     * 读取或设置会话ID
     *
     * @param string $id
     * @return string
     */
    public static function id($id = null)
    {
        return isset($id) ? session_id($id) : session_id();
    }

    /**
     * 读取会话数据
     *
     * @param string $name
     * @return mixed|NULL
     */
    public static function get($name)
    {
        if (! is_string($name)) {
            throw new Exceptions('The session name must be a string.');
        }
        
        if (isset($_SESSION[$name])) {
            return $_SESSION[$name];
        }
        
        return null;
    }

    /**
     * 写入会话数据
     *
     * @param string $name
     * @param mixed $value
     * @throws Exceptions
     * @return boolean
     */
    public static function set($name, $value)
    {
        if (! is_string($name)) {
            throw new Exceptions('The session name must be a string.');
        }
        
        if ($value == null) {
            unset($_SESSION[$name]);
        } else {
            $_SESSION[$name] = $value;
        }
        
        return true;
    }

    /**
     * 检查会话数据是否存在
     *
     * @param string $name
     * @throws Exceptions
     * @return boolean
     */
    public static function is_set($name)
    {
        if (! is_string($name)) {
            throw new Exceptions('The session name must be a string.');
        }
        
        return isset($_SESSION[$name]);
    }

    /**
     * 销毁会话
     *
     * @return boolean
     */
    public static function destroy()
    {
        if (isset($_SESSION)) {
            unset($_SESSION);
            session_destroy();
        }
        
        return true;
    }
    
    /**
     * 写入会话数据并关闭会话连接
     * 
     * @return boolean
     */
    public static function commit()
    {
        return session_commit();
    }
    
    /**
     * 获取会话状态
     * 
     * @return int
     */
    public static function status()
    {
        if (version_compare(PHP_VERSION, '5.4.0', '<')) {
            throw new Exceptions('Not support session_status().');
        }
        
        return session_status();
    }
}
