<?php
namespace PHPSTORM_META {
    use suframe\core\components\rpc\SRpcInterface;
    override( SRpcInterface::route(0),
        map( [
            '/user' => \app\runtime\User::class,
        ]));
}

namespace app\runtime;
//paths
interface User{
    /**
     * @return \app\runtime\rpc\user\UserRpc
     */
    public function user();
}
//rpc
namespace app\runtime\rpc\user;
interface UserRpc {

    /**
     * @param $name
     * @return mixed
     */
    public function search($name);

}
