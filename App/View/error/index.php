<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?php echo $this->title;?></title>
</head>
<body>
  <h1><?php echo $this->title;?></h1>
  <p><?php echo $this->info;?>（代码：<?php echo $this->error['code'];?>）</p>
  <hr />
  <p>Powered by MiniFramework. <?php echo date('r');?></p>
</body>
</html>
