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
            ['id' => 1, 'name' => 'orders1'],
            ['id' => 2, 'name' => 'orders2'],
        ];
    }

}