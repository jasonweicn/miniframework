<h1>Example: Log</h1>
<p>当前环境配置日志功能状态：<b><?php echo $this->log_status;?></b></p>
<p>当前环境配置日志存储方式：<b><?php echo $this->log_mode;?></b></p>
<p>当前环境配置日志存储路径：<b><?php echo LOG_PATH;?></b> （存储方式为文件时有效）</p>
<p>提示：请确保在日志功能开启的状态下，刷新本页面，然后可以去上述路径或数据库中（根据配置的日志存储方式）查看记录的日志信息：<br>
<b><?php echo $this->message;?></b></p>
