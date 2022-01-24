<p>{$info}</p>
{beginBlock:jscode}
<script>
/*
这是一个 Block 的示例
  通常我们会希望 js 代码放到页面底部运行，
  在使用布局的情况下，可以在 View 中通过 beginBlock 和 endBlock 预定义一个代码块，
  在 Layout 文件中，可以通过 inserBlock 将对应的代码块插入到需要的地方。（请见 Layout/default.php）
*/
console.log('this is block');
</script>
{endBlock}
