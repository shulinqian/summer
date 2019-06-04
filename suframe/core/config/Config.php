<?php
/**
 * User: qian
 * Date: 2019/6/4
 * Time: 22:46
 */
namespace suframe\core\config;

/**
 * 配置对象，扩展了merge功能
 * Class Config
 * @package suframe\core\config
 */
class Config extends \Noodlehaus\Config {

    /**
     * 合并data
     *
     * @param array $data
     * @return Config
     */
    public function mergeData(array $data)
    {
        $this->data = array_replace_recursive($this->data, $data);
        return $this;
    }

    /**
     * 合并文件
     * @param $file
     * @param null $name
     * @return Config
     */
    public function mergeFile($file, $name = null){
        if(!is_file($file)){
            return $this;
        }
        if(!$name){
            $name = substr($file, strrpos($file, DIRECTORY_SEPARATOR) + 1, -4);
        }
        $config = new \Noodlehaus\Config($file);
        return $this->mergeData([
            $name => $config->all()
        ]);
    }

    /**
     * 合并多个文件
     * @param $files
     * @return $this
     */
    public function mergeFiles($files){
        foreach ($files as $name => $file) {
            $this->mergeFile($file, $name);
        }
        return $this;
    }

}