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

class Log
{

    /**
     * 错误等级
     * 
     * @var string
     */
    const EMERG     = 'EMERG';
    const ALERT     = 'ALERT';
    const CRIT      = 'CRIT';
    const ERROR     = 'ERROR';
    const WARNING   = 'WARNING';
    const NOTICE    = 'NOTICE';
    const INFO      = 'INFO';
    const DEBUG     = 'DEBUG';
    
    /**
     * Log Instance
     *
     * @var Params
     */
    protected static $_instance;
    
    /**
     * 日志数组
     * 
     * @var array
     */
    protected static $_logs = [];

    /**
     * 获取实例
     */
    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
            register_shutdown_function([Log::getInstance(), 'write']);
        }
        return self::$_instance;
    }
    
    /**
     * 记录日志
     * 
     * @param string $message
     * @param string $level
     * @param array $position ['file' => __FILE__, 'line' => __LINE__]
     */
    public static function record($message, $level = self::ERROR, $position = null)
    {
        if (self::checkLevel($level) === true) {
            self::$_logs[] = [
                'time' => date('Y-m-d H:i:s'),
                'level' => $level,
                'body' => $message,
                'file' => isset($position['file']) ? $position['file'] : null,
                'line' => isset($position['line']) ? $position['line'] : null
            ];
        }
    }
    
    /**
     * 写入日志
     */
    public function write()
    {
        if (LOG_MODE === 1) {
            $this->writeToFile();
        } elseif (LOG_MODE === 2) {
            $this->writeToDb();
        }
    }
    
    /**
     * 检查日志级别
     * 
     * @param string $level
     * @return boolean
     */
    public static function checkLevel($level)
    {
        return in_array($level, explode(',', LOG_LEVEL));
    }
    
    /**
     * 写入日志到文件
     * 
     * @throws Exception
     */
    private function writeToFile()
    {
        $c = count(self::$_logs);
        $t = isset(self::$_logs[$c - 1]['time']) ? strtotime(self::$_logs[$c - 1]['time']) : time();
        is_dir(LOG_PATH) or @mkdir(LOG_PATH, 0700, true);
        $logFile = LOG_PATH . DS . date('Y-m-d', $t) . '.log';
        foreach (self::$_logs as $log) {
            $result = file_put_contents(
                $logFile,
                "{$log['time']} - [{$log['level']}: {$log['body']}] - [F: {$log['file']}][L: {$log['line']}]" . PHP_EOL,
                FILE_APPEND | LOCK_EX
                );
            if ($result === false) {
                throw new Exception('Log write fail.');
            }
        }
    }
    
    /**
     * 写入日志到数据库
     */
    private function writeToDb()
    {
        if (! empty(self::$_logs)) {
            $dbParams = \Mini\Base\Config::getInstance()->load(LOG_DB_CONFIG);
            $db = new \Mini\Db\Mysql($dbParams);
            if ($db->checkTableIsExist(LOG_TABLE_NAME) === false) {
                $sql = "CREATE TABLE `" . LOG_TABLE_NAME . "` (
                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                `time` datetime DEFAULT NULL,
                `level` varchar(16) DEFAULT NULL,
                `body` text DEFAULT NULL,
                `file` varchar(255) DEFAULT NULL,
                `line` int(10) unsigned DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=" . $dbParams['charset'] . ";";
                $db->execSql($sql);
            }
            $db->insertAll(LOG_TABLE_NAME, self::$_logs);
        }
    }
}
