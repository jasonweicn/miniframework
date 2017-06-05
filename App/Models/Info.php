<?php
class Info extends Model
{
    public function getInfo()
    {
        //如果你开启了数据库自动连接功能，就可以用下面这行代码自动加载数据库对象了
        //$db = $this->loadDb('default');
        
        return "Hello World!";
    }
}
?>
