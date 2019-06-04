<?php
namespace suframe\core\console;

use Noodlehaus\ConfigInterface;
use suframe\core\traits\Singleton;
use suframe\core\config\Config as coreConfig;

/**
 * WorkerFee
 * @package app\api\\job
 * @method get($key, $default = null)
 * @method set($key, $value)
 * @method has($key)
 * @method merge(ConfigInterface $config)
 * @method all()
 */
class Config{
	use Singleton;

	/**
	 * @var coreConfig
	 */
	protected $entity;

    /**
     * @param $file
     * @param null $name
     * @return coreConfig
     */
	public function load($file, $name = null){
		if(!$this->entity){
            $this->entity = new coreConfig([]);
            $this->entity->mergeData(['console' => $this->getConsoleDefault()]);
		}
        $this->entity->mergeFile($file, $name) ;
	}


	public function __call($name, $arguments) {
		// TODO: Implement __call() method.
		return $this->entity->$name(...$arguments);
	}

	/**
	 * @return coreConfig
	 */
	public function getEntity(): coreConfig {
		return $this->entity;
	}

    /**
     * 命令行默认配置，可在app/config/console文件中重置
     * @return array
     */
	public function getConsoleDefault(){
        return [
            'name' => 'summer framework',
            'description' => 'welcome to use summer framework',
            'debug' => \Inhere\Console\Console::VERB_ERROR,
            'profile' => false,
            'version' => '0.0.1',
            'publishAt' => '2019.06.04',
            'updateAt' => '2019.06.04',
            'rootPath' => '',
            'strictMode' => false,
            'hideRootPath' => true,

            // 'timeZone' => 'Asia/Shanghai',
            // 'env' => 'prod', // dev test prod
            // 'charset' => 'UTF-8',

            'logoText' => $this->getLogo(),
            'logoStyle' => \Inhere\Console\Component\Style\Style::SUCCESS,
        ];
    }

    /**
     * logo string
     * @return string
     */
    public function getLogo(){
        return <<<EOF
  _____                                       ______                        
 / ____|                                     |  ____|                       
| (___  _   _ _ __ ___  _ __ ___   ___ _ __  | |__ _ __ __ _ _ __ ___   ___ 
 \___ \| | | | '_ ` _ \| '_ ` _ \ / _ \ '__| |  __| '__/ _` | '_ ` _ \ / _ \
 ____) | |_| | | | | | | | | | | |  __/ |    | |  | | | (_| | | | | | |  __/
|_____/ \__,_|_| |_| |_|_| |_| |_|\___|_|    |_|  |_|  \__,_|_| |_| |_|\___|
EOF;
    }

}