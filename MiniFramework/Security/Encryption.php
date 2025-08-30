<?php
// +---------------------------------------------------------------------------
// | Mini Framework
// +---------------------------------------------------------------------------
// | Copyright (c) 2015-2025 http://www.sunbloger.com
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
namespace Mini\Security;

use Mini\Base\Exception;

class Encryption
{
    /**
     * 检查 OpenSSL 扩展是否已启用
     *
     * @return bool
     */
    private function checkOpenSslExtension()
    {
        if (!extension_loaded('openssl')) {
            throw new Exception('The OpenSSL extension is not enabled.');
        }
        
        return true;
    }
    
    /**
     * 检查特定的 OpenSSL 函数是否存在
     *
     * @param string $functionName 函数名称
     * @return bool
     */
    private function checkOpenSslFunction($functionName)
    {
        if (!function_exists($functionName)) {
            throw new Exception('The function "' . $functionName . '" is not available.');
        }
        
        return true;
    }
    
    /**
     * 获取 IV 长度
     *
     * @param string $algorithm 加密算法
     * @return int IV 长度
     */
    private function getIvLength($algorithm)
    {
        $this->checkOpenSslFunction('openssl_cipher_iv_length');
        
        return openssl_cipher_iv_length($algorithm);
    }
    
    /**
     * 加密数据
     *
     * @param string $plaintext 明文数据
     * @param string $key 密钥
     * @param string $algorithm 加密算法，默认为 'aes-256-gcm'
     * @return string|false 加密后的密文或 false
     */
    public function encryptData($plaintext, $key, $algorithm = 'aes-256-gcm')
    {
        // 检查 OpenSSL 扩展是否已启用
        $this->checkOpenSslExtension();
        
        // 检查所需函数是否存在
        $this->checkOpenSslFunction('openssl_random_pseudo_bytes');
        $this->checkOpenSslFunction('openssl_encrypt');
        
        // 生成随机的 IV
        $ivLength = $this->getIvLength($algorithm);
        $iv = openssl_random_pseudo_bytes($ivLength);
        
        // 根据加密算法进行加密
        if ($algorithm === 'aes-256-gcm') {
            $tag = null;
            $ciphertext = openssl_encrypt($plaintext, $algorithm, $key, OPENSSL_RAW_DATA, $iv, $tag);
            $encrypted = $ciphertext . $tag;
        } else {
            $ciphertext = openssl_encrypt($plaintext, $algorithm, $key, OPENSSL_RAW_DATA, $iv);
            $encrypted = $ciphertext;
        }
        
        return base64_encode($iv . $encrypted);
    }
    
    /**
     * 解密数据
     *
     * @param string $ciphertext 密文数据
     * @param string $key 密钥
     * @param string $algorithm 加密算法，默认为 'aes-256-gcm'
     * @return string|false 解密后的明文或 false

     */
    public function decryptData($ciphertext, $key, $algorithm = 'aes-256-gcm')
    {
        // 检查 OpenSSL 扩展是否已启用
        $this->checkOpenSslExtension();
        
        // 检查所需函数是否存在
        $this->checkOpenSslFunction('openssl_decrypt');
        
        // 解码 Base64 编码的密文
        $decoded = base64_decode($ciphertext);
        
        // 提取 IV
        $ivLength = $this->getIvLength($algorithm);
        $iv = substr($decoded, 0, $ivLength);
        
        // 提取加密后的数据
        $encrypted = substr($decoded, $ivLength);
        
        // 根据加密算法进行解密
        if ($algorithm === 'aes-256-gcm') {
            $tag = substr($encrypted, -16);
            $ciphertext = substr($encrypted, 0, -16);
            $plaintext = openssl_decrypt($ciphertext, $algorithm, $key, OPENSSL_RAW_DATA, $iv, $tag);
        } else {
            $plaintext = openssl_decrypt($encrypted, $algorithm, $key, OPENSSL_RAW_DATA, $iv);
        }
        
        return $plaintext;
    }
}
