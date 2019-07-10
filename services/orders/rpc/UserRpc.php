<?php

namespace app\rpc;

class UserRpc
{

    /**
     * 用户接口
     *
     * @param array $name
     * @param array $cond
     * @return array
     */
    public function search(string $name,array $cond = []): array
    {
        return [
            ['id' => 1, 'name' => 'orders1'],
            ['id' => 2, 'name' => 'orders2'],
        ];
    }

}