<?php
// +------------------------------------------------------------
// | Mini Framework
// +------------------------------------------------------------
// | Source: https://github.com/jasonweicn/MiniFramework
// +------------------------------------------------------------
// | Author: Jason.wei <jasonwei06@hotmail.com>
// +------------------------------------------------------------

class Layout
{
    /**
     * Exceptions实例
     * @var Exceptions
     */
    private $_exception;
    
    /**
     * 布局变量容器
     * 
     * @var array
     */
    protected $_container;
    
    /**
     * 布局文件路径
     * 
     * @var mixed
     */
    private $_layout_path;
    
    /**
     * 布局名称
     * @var string
     */
    protected $_layout;
    
    /**
     * Layout Instance
     *
     * @var Layout
     */
    protected static $_instance;
    
    /**
     * 获取实例
     *
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
    function __construct()
    {
        $this->_exception = Exceptions::getInstance();
    }
    
    public function __set($key, $value)
    {
        $this->_container[$key] = $value;
    }
    
    public function __get($key)
    {
        if (isset($this->_container[$key])) {
            return $this->_container[$key];
        }

        return null;
    }
    
    /**
     * 设置布局文件所在路径
     * 
     * @param mixed $path
     */
    public function setLayoutPath($path)
    {
        $this->_layout_path = (string) $path;
        
        return $this;
    }
    
    /**
     * 获取布局文件所在路径
     * 
     */
    public function getLayoutPath()
    {
        return $this->_layout_path;
    }
    
    /**
     * 设置布局
     * 
     */
    public function setLayout($name)
    {
        $this->_layout = (string) $name;
        
        return $this;
    }
    
    /**
     * 获取布局
     * 
     * @param string $name
     */
    public function getLayout()
    {
        return $this->_layout;
    }
    
    /**
     * 获取布局脚本
     * 
     * @param string $layoutScript
     */
    public function getLayoutScript()
    {
        $layoutScript = $this->getLayoutPath() . DIRECTORY_SEPARATOR . $this->getLayout() . '.php';
        if (!file_exists($layoutScript)) {
            if ($this->_exception->throwExceptions()) {
                throw new Exception('Layout "' . $this->getLayout() . '" does not exist.');
            } else {
                $this->_exception->sendHttpStatus(404);
            }
        }
        
        return $layoutScript;
    }
}
