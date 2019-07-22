<?php
namespace app\api;

use suframe\core\components\rpc\SRpc;

class Search{

    public function hello(){
        return 'hello demo api';
    }

    public function rpcHello(){
        SRpc::route('/demo/DemoRpc')->getList('summer!');
    }

}