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
     * 保存的文件名长度
     * @var int
     */
    private $_saveNameLen = 8;
    
    /**
     * 错误信息
     * @var multitype: string | array
     */
    private $_errorMsg;
    
    /**
     * 构造
     * 
     * @param array $params
     * @throws Exception
     */
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
                throw new Exception('Set upload max size error.');
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
    
    /**
     * 保存文件
     * 
     * @param array $files
     * @return array
     */
    public function save($files)
    {
        if (isset($files['tmp_name'])) {
            return $this->saveOne($files);
        }
        
        $fileArray = $this->convertFileArray($files);
        $info = [];
        foreach ($fileArray as $key => $file) {
            if (isset($file['tmp_name'])) {
                $info[$key] = $this->saveOne($file, $key);
            } else {
                if (is_array($file)) {
                    foreach ($file as $subKey => $subFile) {
                        $info[$key][$subKey] = $this->saveOne($subFile, $key.':'.$subKey);
                    }
                }
            }
        }
        
        return $info;
    }
    
    /**
     * 保存单个文件
     * 
     * @param array $file
     * @param string $fileKey
     * @throws Exception
     * @return boolean | array
     */
    public function saveOne($file, $fileKey = null)
    {
        if (! isset($file['tmp_name'])) {
            $this->setErrorMsg('Upload fail: No uploaded files.', $fileKey);
            return false;
        }
        
        if (! is_uploaded_file($file['tmp_name'])) {
            $this->setErrorMsg('Upload fail: No uploaded files.', $fileKey);
            return false;
        }
        
        if ($file['size'] > $this->maxSize) {
            $this->setErrorMsg('Upload fail: Exceed the maximum size.', $fileKey);
            return false;
        }
        
        $fileExtName = strtolower(getFileExtName($file['name']));
        if (! in_array($fileExtName, explode(',', $this->allowType))) {
            $this->setErrorMsg('Upload fail: The "'.$fileExtName.'" is not allowed.', $fileKey);
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
                throw new Exception('Upload fail: Permission denied.(' . $path . ')');
            }
            $path .= DS . $dir;
            if (! file_exists($path) && ! is_dir($path)) {
                if (! mkdir($path, 0700, true)) {
                    $this->setErrorMsg('Upload fail: Create dir fail.(' . $path . DS . $this->saveName . ')', $fileKey);
                    return false;
                }
            }
        }
        
        $this->saveName = '';
        $do = true;
        while ($do) {
            $this->saveName = getRandomString($this->_saveNameLen) . '.' . $fileExtName;
            if (! file_exists($path . DS . $this->saveName)) {
                $do = false;
            }
        }
        
        try {
            if (! move_uploaded_file($file['tmp_name'], $path . DS . $this->saveName)) {
                $this->setErrorMsg('Upload fail: Save fail.(' . $path . DS . $this->saveName . ')', $fileKey);
                return false;
            }
        } catch (Exception $e) {
            throw new Exception('Upload fail: ' . $e);
        }
        
        
        $info = array(
            'path' => $path,
            'fileName' => $this->saveName
        );
        
        return $info;
    }
    
    /**
     * 转换文件数组形态
     * 
     * @param array $files
     * @return array
     */
    private function convertFileArray($files)
    {
        $fileArray = [];
        foreach ($files as $key => $file) {
            if (is_array($file['tmp_name'])) {
                $keys  = array_keys($file);
                for ($i = 0; $i < count($file['tmp_name']); $i++) {
                    foreach ($keys as $_key) {
                        $fileArray[$key][$i][$_key] = $file[$_key][$i];
                    }
                    
                }
            } else {
                $fileArray[$key] = $file;
            }
        }
        
        return $fileArray;
    }
    
    /**
     * 设置保存的文件名长度
     * 
     * @param int $len
     * @return boolean
     */
    public function setSaveNameLen($len)
    {
        if (! is_int($len) || $len <= 0) {
            return false;
        }
        
        $this->_saveNameLen = $len;
        
        return true;
    }
    
    private function setErrorMsg($msg, $fileKey = null)
    {
        if ($fileKey == null) {
            $this->_errorMsg = $msg;
        } else {
            $this->_errorMsg[$fileKey] = $msg;
        }
    }
    
    public function getErrorMsg()
    {
        return $this->_errorMsg;
    }
}
