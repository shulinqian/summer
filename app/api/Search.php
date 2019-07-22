<?php
namespace app\api;

use suframe\core\components\rpc\SRpc;

class Search{

    public function hello(){
        return 'hello demo api';
    }

    public function rpcHello(){
        return SRpc::route('/demo/DemoRpc')->getList('summer!');
    }

}