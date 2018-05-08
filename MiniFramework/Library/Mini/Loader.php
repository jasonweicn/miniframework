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

class Loader
{

    /**
     * Loader Instance
     *
     * @var Loader
     */
    protected static $_instance;

    /**
     * 获取实例
     */
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * 构造
     */
    protected function __construct()
    {
        spl_autoload_register(array(
            __CLASS__,
            'Mini\Loader::autoload'
        ));
    }

    /**
     * 自动载入
     *
     * @param string $class            
     */
    public function autoload($class)
    {
        try {
            $this->loadClass($class);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * 载入类
     * 
     * @param string $class            
     * @throws Exceptions
     */
    public static function loadClass($class)
    {
        $className = ltrim($class, '\\');
        $classPath = '';
        $namespace = '';
        $lastPos = strripos($className, '\\');
        
        if ($lastPos !== false) {
            $namespace = substr($className, 0, $lastPos);
            $classPath = str_replace('\\', DS, $namespace);
            $name = strstr($namespace, '\\', true);
            
            if ($name == APP_NAMESPACE) {
                $classPath = APP_PATH . ltrim($classPath, APP_NAMESPACE);
            } else {
                $classPath = LIB_PATH . DS . $classPath;
            }
            
            $className = substr($className, $lastPos + 1);
        } else {
            $classPath = APP_PATH . DS . 'Model';
        }
        
        $classfile = $classPath . DS . $className . '.php';
        
        if (file_exists($classfile)) {
            include_once ($classfile);
        } else {
            throw new Exceptions('Library "' . $className . '" not found.');
        }
        
        return true;
    }
}
