<?php

namespace suframe\register;

use suframe\core\components\Config;
use suframe\core\components\console\Application;
use suframe\core\components\console\Console;
use suframe\core\components\event\EventManager;
use suframe\core\traits\Singleton;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * summer framework ra
 * ra的主程序就是控制台，通过相应的command和controller完成功能
 * Class Core
 * @package suframe\register
 */
class Core extends \suframe\core\Core
{
    use Singleton;
}