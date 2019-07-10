<?php

namespace app\rpc;

/**
 * Class OrdersRpc
 * @package app\rpc
 */
class OrdersRpc
{

    /**
     * 用户接口
     *
     * @param array $name
     * @param array $cond
     * @return array
     */
    public function getList(bool $test, string $name,array $cond = []): array
    {
        return [
            ['id' => 1, 'name' => 'orders1'],
            ['id' => 2, 'name' => 'orders2'],
        ];
    }

}