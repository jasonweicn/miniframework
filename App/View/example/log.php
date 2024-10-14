<h1>Example: Log</h1>
<p>当前环境配置日志功能状态：<strong><?php echo $this->log_status;?></strong></p>
<p>当前环境配置日志存储方式：<strong><?php echo $this->log_mode;?></strong></p>
<p>当前环境配置日志存储路径：<strong><?php echo LOG_PATH;?></strong> （存储方式为文件时有效）</p>
<p>提示：请确保在日志功能开启的状态下，刷新本页面，然后可以去上述路径或数据库中（根据配置的日志存储方式）查看记录的日志信息：<br>
<strong><?php echo $this->message;?></strong></p>
