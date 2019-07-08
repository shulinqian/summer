<?php

namespace app\rpc;

class User
{

    /**
     * @param string $name
     * @return array
     */
    public function search(string $name): array
    {
        return [
            ['id' => 1, 'name' => 'rpc name'],
            ['id' => 2, 'name' => 'rpc name 2'],
        ];
    }

}