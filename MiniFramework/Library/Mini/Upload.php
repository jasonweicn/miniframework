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

class Upload
{

    /**
     * 保存文件的根目录
     *
     * @var string
     */
    public $rootPath;
    
    /**
     * 保存文件的子目录
     * 
     * @var string
     */
    public $savePath;

    /**
     * 文件的大小限制（单位：Byte）
     *
     * @var int
     */
    public $maxSize;
    
    /**
     * 允许的类型
     * 
     * @var string
     */
    public $allowType;
    
    /**
     * 保存的文件名
     * @var string
     */
    public $saveName;
    
    /**
     * 错误信息
     * @var string
     */
    private $_errorMsg;
    
    public function __construct($params = null)
    {
        if (isset($params['rootPath'])) {
            $this->rootPath = $params['rootPath'];
        } else {
            $this->rootPath = PUBLIC_PATH . DS . 'uploads';
        }
        
        if (isset($params['savePath'])) {
            $this->savePath = $params['savePath'];
        } else {
            $this->savePath = '';
        }
        
        if (isset($params['maxSize'])) {
            if (! preg_match('/^\d+$/', $params['maxSize'])) {
                throw new Exceptions('Set upload max size error.');
            }
            $this->maxSize = $params['maxSize'];
        } else {
            $this->maxSize = 2097152;
        }
        
        if (isset($params['allowType'])) {
            $this->allowType = $params['allowType'];
        } else {
            $this->allowType = 'bmp,gif,jpg,jpeg,png';
        }
    }
    
    public function save($file)
    {
        if (! isset($file['tmp_name'])) {
            $this->setErrorMsg('Upload fail: No uploaded files.');
            return false;
        }
        
        if (! is_uploaded_file($file['tmp_name'])) {
            $this->setErrorMsg('Upload fail: No uploaded files.');
            return false;
        }
        
        if ($file['size'] > $this->maxSize) {
            $this->setErrorMsg('Upload fail: Exceed the maximum size.');
            return false;
        }
        
        $fileExtName = strtolower(getFileExtName($file['name']));
        if (! in_array($fileExtName, explode(',', $this->allowType))) {
            $this->setErrorMsg('Upload fail: The "'.$fileExtName.'" is not allowed.');
            return false;
        }
        
        if ($this->savePath == '') {
            $this->savePath = date('Y' . DS . 'm' . DS . 'd');
        }
        
        // check permission & create dir
        $savePathArray = explode(DS, $this->savePath);
        $path = $this->rootPath;
        foreach ($savePathArray as $dir) {
            if (! is_writable($path)) {
                throw new Exceptions('Upload fail: Permission denied.(' . $path . ')');
            }
            $path .= DS . $dir;
            if (! file_exists($path) && ! is_dir($path)) {
                @mkdir($path, 0700);
            }
        }
        
        $this->saveName = '';
        $do = true;
        while ($do) {
            $this->saveName = getRandomString(8) . '.' . $fileExtName;
            if (! file_exists($path . DS . $this->saveName)) {
                $do = false;
            }
        }
        
        $res = @move_uploaded_file($file['tmp_name'], $path . DS . $this->saveName);
        
        if (! $res) {
            $this->setErrorMsg('Upload fail: Save fail.(' . $path . DS . $this->saveName . ')');
            return false;
        }
        
        $info = array(
            'path' => $path,
            'fileName' => $this->saveName
        );
        
        return $info;
    }
    
    private function setErrorMsg($msg)
    {
        $this->_errorMsg = $msg;
    }
    
    public function getErrorMsg()
    {
        return $this->_errorMsg;
    }
}
