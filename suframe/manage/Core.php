<?php

namespace suframe\manage;

use suframe\core\components\Config;
use suframe\core\components\console\Application;
use suframe\core\components\console\Console;
use suframe\core\traits\Singleton;
use suframe\core\event\EventManager;
use suframe\manage\events\Events;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * summer framework manage
 * manage的主程序就是控制台，通过相应的command和controller完成功能
 * Class Core
 * @package suframe\manage
 */
class Core
{
    use Singleton;

    /**
     * @var Application
     */
    protected $console;

    /**
     * 初始化
     * @return $this
     */
    public function init()
    {
        //初始化系统配置
        $this->loadConfig();
        //注册事件
        $this->registerListener();
        EventManager::get()->trigger(Events::E_CONSOLE_INIT_BEFORE, $this);
        // 注册命令组
        $this->getConsole()->registerGroups('suframe\\manage\\commands', __DIR__ . '/commands/');
        EventManager::get()->trigger(Events::E_CONSOLE_INIT_AFTER, $this);
        return $this;
    }

    /**
     * 控制台启动
     * @param array $args
     * @return void
     * @throws \ReflectionException
     */
    public function run(array $args): void
    {
        EventManager::get()->trigger(Events::E_CONSOLE_RUN_BEFORE, $this);
        $console = $this->getConsole();
        $console->run();
        EventManager::get()->trigger(Events::E_CONSOLE_RUN_AFTER, $this);
    }

    /**
     * 获取console
     * @return Application
     */
    public function getConsole(): Application
    {
        if (!$this->console) {
            $this->console = Console::getInstance()->getApp();
        }
        return $this->console;
    }

    /**
     * 设置console
     * @param Application $console
     */
    public function setConsole(Application $console): void
    {
        $this->console = $console;
    }

    protected $output;

    /**
     * 控制台输出
     * @return mixed
     */
    public function getOutput()
    {
        if ($this->output) {
            return $this->output;
        }
        return $this->output = new ConsoleOutput();
    }

    /**
     * 初始化配置
     */
    protected function loadConfig()
    {
        $config = Config::getInstance([]);
        // 系统配置
        $config->loadFile(__DIR__ . '/config/config.php');
        // ini配置
        $config->loadFile(SUMMER_APP_ROOT . 'summer.ini');
        // 应用自定义
        $selfConfig = SUMMER_APP_ROOT . 'config/config.php';
        $config->loadFile($selfConfig);
    }

    /**
     * 注册事件
     */
    protected function registerListener()
    {
        $config = Config::getInstance();
        $listeners = $config->get('listener');
        if (!$listeners) {
            return;
        }
        $eventManager = EventManager::get();
        foreach ($listeners as $listener) {
            (new $listener)->attach($eventManager);
        }
    }
}