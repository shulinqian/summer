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
     * @return string
     */
    public function getList(string $name): string
    {
        return 'hello ' . $name;
    }

}