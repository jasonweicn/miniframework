<h1>Example: Session</h1>
<p>Current timestamp: <?php echo $this->t;?></p>
<p>Session timestamp: <?php echo $this->session_time;?></p>
<p>Session ID：<?php echo $this->session_id?></p>
<p>说明：这个示例会将第一次访问的时间戳存入 Session，后续再次刷新页面时，Session 中存储的时间戳不会再出现变化。</p>