<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>Examples for MiniFramework</title>
<style>
table, table tr th, table tr td { border:1px solid #228B22; }
thead { background-color: #F0FFF0;font-weight: bold;}
table { min-height: 25px; line-height: 25px; text-align: center; border-collapse: collapse;}
td {padding: 10px;}
</style>
</head>
<body>
  <table>
    <thead>
      <td>序号</td>
      <td>示例说明</td>
      <td>演示链接</td>
    </thead>
    <tbody>
      <tr>
        <td>1</td>
        <td>验证码（Captcha）</td>
        <td><a href="<?php echo $this->baseUrl();?>/example/captcha" target="_blank"><?php echo $this->baseUrl();?>/example/captcha</a></td>
      </tr>
      <tr>
        <td>2</td>
        <td>会话（Session）</td>
        <td><a href="<?php echo $this->baseUrl();?>/example/session" target="_blank"><?php echo $this->baseUrl();?>/example/session</a></td>
      </tr>
      <tr>
        <td>3</td>
        <td>上传文件（Upload）</td>
        <td><a href="<?php echo $this->baseUrl();?>/example/upload" target="_blank"><?php echo $this->baseUrl();?>/example/upload</a></td>
      </tr>
      <tr>
        <td>4</td>
        <td>日志（Log）</td>
        <td><a href="<?php echo $this->baseUrl();?>/example/log" target="_blank"><?php echo $this->baseUrl();?>/example/log</a></td>
      </tr>
      <tr>
        <td>5</td>
        <td>调试：计时器（Debug timer）</td>
        <td><a href="<?php echo $this->baseUrl();?>/example/debugtimer" target="_blank"><?php echo $this->baseUrl();?>/example/debugtimer</a></td>
      </tr>
      <tr>
        <td>6</td>
        <td>签名（Sign）</td>
        <td><a href="<?php echo $this->baseUrl();?>/example/sign" target="_blank"><?php echo $this->baseUrl();?>/example/sign</a></td>
      </tr>
      <tr>
        <td>7</td>
        <td>校验签名（Verify sign）</td>
        <td>同上</td>
      </tr>
      <tr>
        <td>8</td>
        <td>路由（Route）</td>
        <td><a href="<?php echo $this->baseUrl();?>/example/route" target="_blank"><?php echo $this->baseUrl();?>/example/route</a></td>
      </tr>
      <tr>
        <td>9</td>
        <td>响应控制（Response）</td>
        <td><a href="<?php echo $this->baseUrl();?>/example/response" target="_blank"><?php echo $this->baseUrl();?>/example/reponse</a></td>
      </tr>
      <tr>
        <td>10</td>
        <td>加密解密（Encryption）</td>
        <td><a href="<?php echo $this->baseUrl();?>/example/encryption" target="_blank"><?php echo $this->baseUrl();?>/example/encryption</a></td>
      </tr>
    </tbody>
  </table>
  <hr />
  <p>
      Powered by MiniFramework
  </p>
</body>
</html>
