<?php

//Service
class News
{

    public function search($cond)
    {
        return [123];
    }

}



//client
//寻址？  route匹配？
//连接池？ 自动创建长连接和连接池？
//代码提示？ 生成Srcp文件？ 还是注释文件？？额。。。
\suframe\core\components\rpc\SRpc::route('/user')->user()->search(111);