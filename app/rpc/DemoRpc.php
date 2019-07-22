<?php

namespace app\rpc;

/**
 * Class DemoRpc
 * @package app\rpc
 */
class DemoRpc
{

    /**
     * demo接口
     *
     * @param string $name
     * @return array
     */
    public function getList(string $name): array
    {
        return ['rpc hello ' . $name];
    }

}