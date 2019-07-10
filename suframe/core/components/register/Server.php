<?php
/**
 * User: qian
 * Date: 2019/7/1 16:36
 */

namespace suframe\core\components\register;

use suframe\core\components\Config;
use suframe\core\components\register\Client as ClientAlias;
use suframe\core\components\rpc\RpcPack;
use suframe\core\components\swoole\ProcessTools;
use suframe\core\traits\Singleton;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;

/**
 * 服务定时同步
 * Class SyncServers
 * @package suframe\register\components
 */
class Server
{
    use Singleton;


    /**
     * @return Config
     */
    public function getConfig(): Config
    {
        return ClientAlias::getInstance()->reloadServer();
    }

    /**
     * @param $args
     * @return bool
     * @throws \Exception
     */
    public function register($args){
        $config = ClientAlias::getInstance()->reloadServer();
        $path = $args['path'];
        $server = $config->get('servers');
        //唯一key防止重复
        $key = md5($args['ip'] . $args['port']);
        if(isset($server[$path])){
            //已存在
            if($server[$path]->get($key)){
                throw new \Exception('exist');
            }
            $server[$path][$key] = ['ip' => $args['ip'], 'port' => $args['port']];
        } else {
            $server[$path] = [
                $key => ['ip' => $args['ip'], 'port' => $args['port']]
            ];
        }
        try{
            //写入api配置
            ClientAlias::getInstance()->updateLocalFile($server);
        } catch (\Exception $e){
            return false;
        }

        //rpc服务更新
        $this->registerRpc($path, $args['rpc'] ?? []);
        //通知服务更新
        $this->notify();
        //重启
        ProcessTools::kill();
    }

    public function registerRpc($path, $rpc){
        if(!$rpc){
            return false;
        }
        $savePath = SUMMER_APP_ROOT . 'runtime/rpc' . $path;
        $fs = new Filesystem();
        if(!is_dir($savePath)){
            //创建目录
            try {
                $fs->mkdir($savePath, '0700');
            } catch (IOExceptionInterface $e) {
                echo "An error occurred while creating your directory at ".$e->getPath();
                return false;
            }
        }
        $nameSpace = 'app\runtime\rpc\\' . ltrim($path, '/') . ';';
        $methods = '';
        foreach ($rpc as $class => $items) {
            foreach ($items as $item) {
                $parameters = [];
                foreach ($item['parameters'] as $parameter) {
                    $str = $parameter['type'] ? $parameter['type'] . ' ' : '';
                    $str .= '$' . $parameter['name'];
                    $str .= $parameter['default'] ? ' = ' . $parameter['default'] : '';
                    $parameters[] = $str;
                }
                $parametersStr = implode(', ', $parameters);
                $methods .= <<<EOF

    {$item['doc']}
    public function {$item['name']} ({$parametersStr});
    
EOF;
            }
            $content = <<<EOF
<?php
namespace {$nameSpace};

interface {$class}
{
{$methods}
}
EOF;
            $fs->dumpFile($savePath . '/' . $class . '.php', $content);
        }
    }

    /**
     * 耿直服务更新
     * @return bool
     */
    public function notify()
    {
        try {
            $servers = $this->getConfig()->get('servers');
            //通知更新
            $pack = new RpcPack('/summer/client/notify');
            $pack->add('command', ClientAlias::COMMAND_UPDATE_SERVERS);
            foreach ($servers as $server) {
                /** @var Config $item */
                foreach ($server as $key => $item) {
                    $client = new \Co\Client(SWOOLE_SOCK_TCP);
                    if (!$client->connect($item['ip'], $item['port'], 0.5)) {
                        exit("connect failed. Error: {$client->errCode}\n");
                    }
                    $client->send($pack->pack());
//                    echo $client->recv();
                    $client->close();
                }
            }
        } catch (\Exception $e) {
        }
    }

}