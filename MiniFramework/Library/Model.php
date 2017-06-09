<?php
// +------------------------------------------------------------
// | Mini Framework
// +------------------------------------------------------------
// | Source: https://github.com/jasonweicn/MiniFramework
// +------------------------------------------------------------
// | Author: Jason.wei <jasonwei06@hotmail.com>
// +------------------------------------------------------------

namespace Mini;

abstract class Model
{
    /**
     * 数据库对象
     * 
     * @var object
     */
    protected $_db;
    
    /**
     * 构造
     * 
     * @return Action
     */
    function __construct()
    {        
        if (DB_AUTO_CONNECT === true) {
            $this->_db = Action::getInstance()->_db;
        }
    }
    
    public function loadDb($key)
    {
        if (!isset($this->_db[$key])) {
            return null;
        }
        
        return $this->_db[$key];
    }
}
