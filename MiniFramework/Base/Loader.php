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
namespace Mini\Base;

class Loader
{

    /**
     * Loader Instance
     *
     * @var Loader
     */
    protected static $_instance;
    
    /**
     * 函数库清单数组
     *
     * @var array
     */
    private static $_funcs = [];

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
        spl_autoload_register([
            __CLASS__,
            'Mini\Base\Loader::autoload'
        ]);
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
     * @throws Exception
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
                $classPath = MINI_PATH . DS . str_replace($name . '\\', '', $namespace);;
            }
            
            $className = substr($className, $lastPos + 1);
        } else {
            $classPath = APP_PATH . DS . 'Model';
        }
        
        $classfile = $classPath . DS . $className . '.php';
        
        if (file_exists($classfile)) {
            include_once ($classfile);
        } else {
            throw new Exception('Library "' . $className . '" not found.');
        }
        
        return true;
    }
    
    /**
     * 加载函数库
     *
     * @param string $func
     * @throws Exception
     * @return boolean
     */
    public static function loadFunc($func)
    {
        $file = APP_PATH . DS . 'Function' . DS . ucfirst($func) . '.func.php';
        
        $key = md5($file);
        if (! isset(self::$_funcs[$key])) {
            if (file_exists($file)) {
                include ($file);
                self::$_funcs[$key] = true;
            } else {
                throw new Exception('Function "' . $func . '" not found.');
            }
        }
        
        return true;
    }
}
