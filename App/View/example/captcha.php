<?php if (isset($this->code)) echo '<p>input code: ' . $this->code . '</p>';?>
<?php if (isset($this->info)) echo '<p>check result: ' . $this->info . '</p>';?>
<img src="getcaptcha" onclick="this.src='getcaptcha?t='+Math.random()" />
<form method="post" action="captcha">
  <p>code:<input type="text" name="code" value="" />
  <input type="submit" value="check" /></p>
</form>