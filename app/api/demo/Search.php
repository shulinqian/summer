<?php
namespace app\api\demo;

use suframe\core\components\rpc\SRpc;

class Search{

    public function run(){
        return SRpc::route('/demo/DemoRpc')->getList('summer!');
    }

}