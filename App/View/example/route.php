<h1>Example: Route</h1>
<p>这是一个自定义路由的示例</p>
<p>
    首先，请将
    <pre style="color:red;"><?php echo htmlspecialchars("'info-<id:\d+>.html' => 'example/route'");?></pre>
    添加到 Config/route.php 路由配置文件中（默认配置已经存在）。</p>
<p>然后，访问类似 <span style="color:red;"><a href="<?php echo $this->baseUrl();?>/info-12345.html">http://你的域名/info-12345.html</a></span> 这样美化后的地址时（info-后的数字可以自定义），会被路由到 example 控制器的 route 动作方法中。</p>
<p>当通过美化地址访问时，可以提取到id数值：<?php echo $this->id;?></p>
